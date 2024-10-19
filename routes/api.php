<?php

use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AssociationController;
use App\Http\Controllers\Api\Auth\AssociationAuthController;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ProjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('api/v1')->group(function () {
    Route::prefix('/associations')->group(function () {
        Route::post('/register', [AssociationController::class, 'register']);
        Route::post('/verify-otp', [AssociationController::class, 'verifyOtp']);
        Route::post('/resend-otp', [AssociationAuthController::class, 'resendOtp']);
        Route::post('/login', [AssociationAuthController::class, 'resetPassword']);
        Route::post('/reset-password', [AssociationAuthController::class, 'resetPassword']);
        Route::post('/forgot-password', [AssociationAuthController::class, 'forgotPassword']);

    });
    Route::post('/companies/register', [CompanyController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('projects', ProjectController::class);
        Route::put('/companies/preferences', [CompanyController::class, 'updatePreferences']);
        Route::post('/campaigns', [CampaignController::class, 'create']);
        Route::post('/campaigns/{campaign}/vote', [CampaignController::class, 'vote']);
        Route::get('/campaigns/{campaign}/results', [CampaignController::class, 'results']);

    });

    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::post('/admin/companies/{company}/allocate-projects', [AdminController::class, 'allocateProjects']);
    });

    Route::prefix('admin')->group(function () {
        Route::post('/register', [AdminAuthController::class, 'register']);
        Route::post('/login', [AdminAuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AdminAuthController::class, 'logout']);
            Route::get('/me', [AdminAuthController::class, 'me']);
        });
    });
});