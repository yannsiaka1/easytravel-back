<?php

use App\Http\Controllers\API\AgentController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ReservationController;
use App\Http\Controllers\API\VoyageController;
use App\Http\Controllers\PaiementController;
use Illuminate\Support\Facades\Route;

// Authentification publique.
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Tous les utilisateurs connectés peuvent consulter les voyages à venir.
    Route::get('/voyages', [VoyageController::class, 'index']);

    Route::middleware('role:client')->group(function () {
        Route::prefix('reservations')->group(function () {
            Route::get('/', [ReservationController::class, 'index']);
            Route::post('/', [ReservationController::class, 'store']);
            Route::get('/{reservation}', [ReservationController::class, 'show'])->whereNumber('reservation');
            Route::post('/disponibles', [ReservationController::class, 'disponibles']);
        });

        Route::get('/reservation-a-payer', [PaiementController::class, 'reservationAPayer']);
        Route::post('/paiement', [PaiementController::class, 'effectuerPaiement']);
    });

    Route::middleware('role:agent')->group(function () {
        Route::post('/voyages', [VoyageController::class, 'store']);
        Route::put('/voyages/{voyage}', [VoyageController::class, 'update']);
        Route::delete('/voyages/{voyage}', [VoyageController::class, 'destroy']);

        Route::prefix('agent')->group(function () {
            Route::get('/statistiques', [AgentController::class, 'statistiquesGlobales']);
            Route::get('/graphiques', [AgentController::class, 'graphiquesReservationPaiement']);
            Route::get('/repartition-classe', [AgentController::class, 'repartitionParClasse']);
            Route::get('/reservations', [AgentController::class, 'reservationsParStatut']);

            Route::get('/utilisateurs', [AgentController::class, 'tousLesUtilisateurs']);
            Route::post('/utilisateurs', [AgentController::class, 'creerUtilisateur']);
            Route::get('/utilisateurs/{utilisateur}', [AgentController::class, 'utilisateurParId']);
            Route::put('/utilisateurs/{utilisateur}', [AgentController::class, 'modifierUtilisateur']);
            Route::delete('/utilisateurs/{utilisateur}', [AgentController::class, 'supprimerUtilisateur']);

            Route::get('/tickets', [AgentController::class, 'tousLesTickets']);
            Route::get('/paiements', [AgentController::class, 'tousLesPaiements']);
            Route::get('/profil', [AgentController::class, 'profilAgent']);
            Route::put('/profil', [AgentController::class, 'modifierProfilAgent']);
        });
    });
});
