<?php
// app/Http/Controllers/VAERequestController.php

namespace App\Http\Controllers;

use App\Models\VAERequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class VAERequestController extends Controller
{
    /**
     * Récupérer toutes les demandes VAE (Admin)
     */
    public function index(Request $request)
    {
        $query = VAERequest::query();

        // Filtres
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('domain') && $request->domain) {
            $query->where('domain', $request->domain);
        }

        if ($request->has('diploma') && $request->diploma) {
            $query->where('target_diploma', $request->diploma);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('field', 'LIKE', "%{$search}%");
            });
        }

        // Tri
        $sortField = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        // Pagination
        $perPage = $request->input('per_page', 20);
        $vaeRequests = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $vaeRequests,
            'meta' => [
                'total' => $vaeRequests->total(),
                'per_page' => $vaeRequests->perPage(),
                'current_page' => $vaeRequests->currentPage(),
                'last_page' => $vaeRequests->lastPage(),
            ]
        ]);
    }

    /**
     * Récupérer les statistiques des demandes VAE (Admin)
     */
    public function stats()
    {
        $stats = [
            'total' => VAERequest::count(),
            'pending' => VAERequest::pending()->count(),
            'reviewing' => VAERequest::where('status', 'reviewing')->count(),
            'contacted' => VAERequest::where('status', 'contacted')->count(),
            'documents' => VAERequest::where('status', 'documents')->count(),
            'approved' => VAERequest::where('status', 'approved')->count(),
            'rejected' => VAERequest::where('status', 'rejected')->count(),
            'by_domain' => [],
            'by_diploma' => [],
            'recent' => VAERequest::orderBy('created_at', 'desc')->limit(10)->get(),
        ];

        // Statistiques par domaine
        foreach (VAERequest::DOMAINS as $key => $label) {
            $stats['by_domain'][$key] = [
                'label' => $label,
                'count' => VAERequest::byDomain($key)->count(),
            ];
        }

        // Statistiques par diplôme
        foreach (VAERequest::TARGET_DIPLOMAS as $key => $label) {
            $stats['by_diploma'][$key] = [
                'label' => $label,
                'count' => VAERequest::byDiploma($key)->count(),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Soumettre une nouvelle demande VAE (Public)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullName' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'city' => 'nullable|string|max:255',
            'experienceYears' => 'required|in:lt3,3-5,5-10,gt10',
            'domain' => 'required|in:commerce,informatique,sante,btp,management,finance,autre',
            'experience' => 'required|string|min:150',
            'targetDiploma' => 'required|in:bts,licence,master,titre',
            'field' => 'required|string|max:255',
            'message' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Créer la demande
            $vaeRequest = VAERequest::create([
                'full_name' => $request->fullName,
                'email' => $request->email,
                'phone' => $request->phone,
                'city' => $request->city,
                'experience_years' => $request->experienceYears,
                'domain' => $request->domain,
                'experience' => $request->experience,
                'target_diploma' => $request->targetDiploma,
                'field' => $request->field,
                'message' => $request->message,
                'status' => 'pending',
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);

            // Envoyer une confirmation par email (optionnel)
            try {
                Mail::send('emails.vae-confirmation', [
                    'name' => $request->fullName,
                    'email' => $request->email,
                ], function($mail) use ($request) {
                    $mail->to($request->email)
                         ->subject('Confirmation de votre demande VAE - IFDCE');
                });

                // Notification à l'admin
                Mail::send('emails.vae-admin-notification', [
                    'vaeRequest' => $vaeRequest,
                ], function($mail) {
                    $mail->to(env('ADMIN_EMAIL', 'admin@ifdce.com'))
                         ->subject('Nouvelle demande VAE - IFDCE');
                });
            } catch (\Exception $e) {
                Log::error('Erreur envoi email VAE: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Votre demande VAE a été envoyée avec succès. Un conseiller vous contactera sous 48h.',
                'data' => $vaeRequest
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur création demande VAE: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer.'
            ], 500);
        }
    }

    /**
     * Afficher une demande VAE spécifique (Admin)
     */
    public function show($id)
    {
        $vaeRequest = VAERequest::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $vaeRequest
        ]);
    }

    /**
     * Mettre à jour le statut d'une demande VAE (Admin)
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,reviewing,contacted,documents,approved,rejected',
            'admin_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $vaeRequest = VAERequest::findOrFail($id);
        
        // Mettre à jour avec la méthode correspondante si disponible
        $status = $request->status;
        $methodMap = [
            'contacted' => 'markAsContacted',
            'documents' => 'markAsDocumentsReceived',
            'approved' => 'markAsApproved',
            'rejected' => 'markAsRejected',
            'reviewing' => 'markAsReviewing',
        ];

        if (isset($methodMap[$status]) && method_exists($vaeRequest, $methodMap[$status])) {
            $vaeRequest->{$methodMap[$status]}();
        } else {
            $vaeRequest->status = $status;
            $vaeRequest->save();
        }

        // Ajouter les notes admin
        if ($request->admin_notes) {
            $vaeRequest->admin_notes = $request->admin_notes;
            $vaeRequest->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Statut de la demande VAE mis à jour',
            'data' => $vaeRequest
        ]);
    }

    /**
     * Ajouter des notes admin (Admin)
     */
    public function addNotes(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'admin_notes' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $vaeRequest = VAERequest::findOrFail($id);
        $vaeRequest->admin_notes = $request->admin_notes;
        $vaeRequest->save();

        return response()->json([
            'success' => true,
            'message' => 'Notes ajoutées avec succès',
            'data' => $vaeRequest
        ]);
    }

    /**
     * Supprimer une demande VAE (Admin)
     */
    public function destroy($id)
    {
        $vaeRequest = VAERequest::findOrFail($id);
        $vaeRequest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Demande VAE supprimée avec succès'
        ]);
    }

    /**
     * Exporter les demandes VAE en CSV (Admin)
     */
    public function export(Request $request)
    {
        $query = VAERequest::query();

        // Appliquer les filtres
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $vaeRequests = $query->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="demandes_vae_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($vaeRequests) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, [
                'ID',
                'Nom complet',
                'Email',
                'Téléphone',
                'Ville',
                'Expérience',
                'Domaine',
                'Diplôme visé',
                'Spécialité',
                'Statut',
                'Créé le',
                'Contacté le',
                'Documents reçus le',
                'Approuvé le',
                'Notes admin'
            ]);

            foreach ($vaeRequests as $request) {
                fputcsv($file, [
                    $request->id,
                    $request->full_name,
                    $request->email,
                    $request->phone,
                    $request->city,
                    $request->experience_label,
                    $request->domain_label,
                    $request->target_diploma_label,
                    $request->field,
                    $request->status_label,
                    $request->created_at->format('d/m/Y H:i'),
                    $request->contacted_at?->format('d/m/Y H:i'),
                    $request->documents_received_at?->format('d/m/Y H:i'),
                    $request->approved_at?->format('d/m/Y H:i'),
                    $request->admin_notes,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}