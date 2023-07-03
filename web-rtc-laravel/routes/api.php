<?php

use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use Pusher\Pusher;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("/register", [AuthController::class, 'register']);
Route::post("/login", [AuthController::class, 'login']);
Route::get("/logged", [AuthController::class, 'loggedUser']);

Route::get('/users', function (){
    $users = User::all();
    return response()->json($users);
});

Route::post('/user/{userId}/offer', function (Request $request, $userId) {
    $offer = $request->input('offer');

    // Envoyer l'offre à l'utilisateur avec l'ID $userId
    // Vous pouvez utiliser un service de diffusion en temps réel (comme Pusher) pour cela

    $pusher = new Pusher(
        env('PUSHER_APP_KEY'),
        env('PUSHER_APP_SECRET'),
        env('PUSHER_APP_ID'),
        [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'encrypted' => true
        ]
    );

    $pusher->trigger('private-channel', 'offer-received', [
        'userId' => $userId,
        'offer' => $offer
    ]);

    return response()->json(['message' => 'Offre envoyée']);
});

Route::post('/user/{userId}/ice-candidate', function (Request $request, $userId) {
    $candidate = $request->input('candidate');

    // Envoyer le candidat ICE à l'utilisateur avec l'ID $userId en utilisant Pusher
    $pusher = new Pusher(
        env('PUSHER_APP_KEY'),
        env('PUSHER_APP_SECRET'),
        env('PUSHER_APP_ID'),
        [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'encrypted' => true
        ]
    );

    $pusher->trigger('private-channel', 'ice-candidate-received', [
        'userId' => $userId,
        'candidate' => $candidate
    ]);

    return response()->json(['message' => 'Candidat ICE envoyé']);
});

// -------------------------------------------------------------------------------------

Route::post('/broadcasting/auth', function () {
    return Broadcast::auth(request());
});

Route::get('/users', function () {
    $users = User::all(); // Récupérez la liste des utilisateurs disponibles depuis votre base de données ou autre source
    return response()->json($users);
});

Broadcast::channel('call.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('call.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
