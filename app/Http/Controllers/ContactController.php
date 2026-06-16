<?php
// app/Http/Controllers/ContactController.php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Récupérer tous les messages de contact (Admin)
     */
    public function index(Request $request)
    {
        $query = Contact::query();

        // Filtres
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('subject', 'LIKE', "%{$search}%")
                  ->orWhere('message', 'LIKE', "%{$search}%");
            });
        }

        // Tri
        $sortField = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        // Pagination
        $perPage = $request->input('per_page', 20);
        $contacts = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $contacts,
            'meta' => [
                'total' => $contacts->total(),
                'per_page' => $contacts->perPage(),
                'current_page' => $contacts->currentPage(),
                'last_page' => $contacts->lastPage(),
            ]
        ]);
    }

    /**
     * Récupérer les statistiques des contacts (Admin)
     */
    public function stats()
    {
        $stats = [
            'total' => Contact::count(),
            'pending' => Contact::pending()->count(),
            'unread' => Contact::unread()->count(),
            'replied' => Contact::where('status', 'replied')->count(),
            'archived' => Contact::where('status', 'archived')->count(),
            'by_type' => [],
        ];

        foreach (Contact::TYPES as $key => $label) {
            $stats['by_type'][$key] = [
                'label' => $label,
                'count' => Contact::byType($key)->count(),
            ];
        }

        $stats['recent'] = Contact::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Envoyer un nouveau message de contact (Public)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'type' => 'required|in:general,vae,formation,conseil,support',
            'message' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Créer le message
            $contact = Contact::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'subject' => $request->subject,
                'type' => $request->type,
                'message' => $request->message,
                'status' => 'pending',
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);

            // Envoyer une notification par email (optionnel)
            try {
                // Envoyer un email de confirmation au visiteur
                Mail::send('emails.contact-confirmation', [
                    'name' => $request->name,
                    'subject' => $request->subject,
                    'message' => $request->message,
                ], function($mail) use ($request) {
                    $mail->to($request->email)
                         ->subject('Confirmation de votre message - IFDCE');
                });

                // Envoyer une notification à l'administrateur
                Mail::send('emails.contact-admin-notification', [
                    'contact' => $contact,
                ], function($mail) {
                    $mail->to(env('ADMIN_EMAIL', 'admin@ifdce.com'))
                         ->subject('Nouveau message de contact - IFDCE');
                });
            } catch (\Exception $e) {
                Log::error('Erreur d\'envoi email contact: ' . $e->getMessage());
                // L'erreur d'email ne bloque pas la création du contact
            }

            return response()->json([
                'success' => true,
                'message' => 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.',
                'data' => $contact
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer ou nous contacter directement par email.'
            ], 500);
        }
    }

    /**
     * Afficher un message spécifique (Admin)
     */
    public function show($id)
    {
        $contact = Contact::findOrFail($id);
        
        // Marquer comme lu si ce n'est pas déjà fait
        if ($contact->status === 'pending' || is_null($contact->read_at)) {
            $contact->markAsRead();
        }

        return response()->json([
            'success' => true,
            'data' => $contact
        ]);
    }

    /**
     * Marquer un message comme répondu (Admin)
     */
    public function markAsReplied($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->markAsReplied();

        return response()->json([
            'success' => true,
            'message' => 'Message marqué comme répondu',
            'data' => $contact
        ]);
    }

    /**
     * Archiver un message (Admin)
     */
    public function archive($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->update(['status' => 'archived']);

        return response()->json([
            'success' => true,
            'message' => 'Message archivé avec succès',
            'data' => $contact
        ]);
    }

    /**
     * Supprimer un message (Admin)
     */
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message supprimé avec succès'
        ]);
    }

    /**
     * Exporter les messages en Excel (Admin)
     */
    public function export(Request $request)
    {
        $query = Contact::query();

        // Appliquer les filtres
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $contacts = $query->orderBy('created_at', 'desc')->get();

        // Générer un fichier Excel avec Maatwebsite Excel (si installé)
        // Sinon, retourner un CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="contacts_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($contacts) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, [
                'ID',
                'Nom',
                'Email',
                'Téléphone',
                'Sujet',
                'Type',
                'Message',
                'Statut',
                'Créé le',
                'Lu le',
                'Répondu le'
            ]);

            // Données
            foreach ($contacts as $contact) {
                fputcsv($file, [
                    $contact->id,
                    $contact->name,
                    $contact->email,
                    $contact->phone,
                    $contact->subject,
                    $contact->type_label,
                    $contact->message,
                    $contact->status_label,
                    $contact->created_at->format('d/m/Y H:i'),
                    $contact->read_at?->format('d/m/Y H:i'),
                    $contact->replied_at?->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}