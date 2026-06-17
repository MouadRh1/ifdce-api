<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DiplomaController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\VAERequestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/


// Authentification
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

// Routes publiques
Route::post('/contact', [ContactController::class, 'store']);
Route::post('/vae/request', [VAERequestController::class, 'store']);

// Routes publiques pour les diplômes et fields
Route::get('/diplomas', [DiplomaController::class, 'index']);
Route::get('/fields', [FieldController::class, 'index']);
Route::get('/diplomas/{diplomaId}/fields', [FieldController::class, 'getFieldsByDiploma']);
Route::get('/available-diplomas', [FieldController::class, 'getAvailableDiplomas']);
Route::get('/diplomas-with-fields', [DiplomaController::class, 'getDiplomasWithFields']);

// Routes API Ressources (publiques) - lecture seule
Route::get('/diplomas/{diploma}', [DiplomaController::class, 'show']);
Route::get('/fields/{field}', [FieldController::class, 'show']);

// Routes protégées par authentification
Route::middleware('auth:sanctum')->group(function () {
    
    // Récupérer l'utilisateur connecté
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Déconnexion
    Route::post('/logout', [LoginController::class, 'logout']);
    
    // Routes Admin (protégées par middleware admin)
    Route::middleware('admin')->group(function () {
        
        // === GESTION DES CONTACTS ===
        Route::get('/admin/contacts', [ContactController::class, 'index']);
        Route::get('/admin/contacts/stats', [ContactController::class, 'stats']);
        Route::get('/admin/contacts/{id}', [ContactController::class, 'show']);
        Route::put('/admin/contacts/{id}/reply', [ContactController::class, 'markAsReplied']);
        Route::put('/admin/contacts/{id}/archive', [ContactController::class, 'archive']);
        Route::delete('/admin/contacts/{id}', [ContactController::class, 'destroy']);
        Route::get('/admin/contacts/export', [ContactController::class, 'export']);
        
        // === GESTION DES DEMANDES VAE ===
        Route::get('/admin/vae-requests', [VAERequestController::class, 'index']);
        Route::get('/admin/vae-requests/stats', [VAERequestController::class, 'stats']);
        Route::get('/admin/vae-requests/{id}', [VAERequestController::class, 'show']);
        Route::put('/admin/vae-requests/{id}/status', [VAERequestController::class, 'updateStatus']);
        Route::put('/admin/vae-requests/{id}/notes', [VAERequestController::class, 'addNotes']);
        Route::delete('/admin/vae-requests/{id}', [VAERequestController::class, 'destroy']);
        Route::get('/admin/vae-requests/export', [VAERequestController::class, 'export']);
        
        // === TABLEAU DE BORD ADMIN ===
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index']);
        
        // === GESTION DES UTILISATEURS ===
        Route::get('/admin/users', [AdminDashboardController::class, 'getUsers']);
        Route::get('/admin/users/{userId}/applications', [AdminDashboardController::class, 'getUserApplications']);
        Route::put('/admin/users/{userId}/role', [AdminDashboardController::class, 'updateUserRole']);
        Route::delete('/admin/users/{userId}', [AdminDashboardController::class, 'deleteUser']);
        
        // === GESTION DES INSCRIPTIONS ===
        Route::get('/admin/enrollments', [AdminDashboardController::class, 'getAllEnrollments']);
        Route::get('/admin/enrollments/by-diploma-field', [AdminDashboardController::class, 'getUsersByDiplomaAndField']);
        Route::get('/admin/enrollments/stats', [AdminDashboardController::class, 'getEnrollmentStats']);
        Route::put('/admin/applications/{applicationId}/status', [AdminDashboardController::class, 'updateApplicationStatus']);
        
        // === GESTION DES DIPLÔMES (CRUD complet) ===
        Route::post('/diplomas', [DiplomaController::class, 'store']);
        Route::put('/diplomas/{diploma}', [DiplomaController::class, 'update']);
        Route::delete('/diplomas/{diploma}', [DiplomaController::class, 'destroy']);
        
        // === GESTION DES FIELDS (CRUD complet) ===
        Route::post('/fields', [FieldController::class, 'store']);
        Route::put('/fields/{field}', [FieldController::class, 'update']);
        Route::delete('/fields/{field}', [FieldController::class, 'destroy']);
    });
});

// Note: Les routes API Ressources (apiResource) doivent être en dernier pour éviter les conflits
// Si vous utilisez apiResource, assurez-vous qu'elles sont définies après les routes personnalisées