<?php

use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AssociationController;
use App\Http\Controllers\Api\Auth\AdminAuthController;
use App\Http\Controllers\Api\Auth\AssociationAuthController;
use App\Http\Controllers\Api\Auth\CompanyAuthController;
use App\Http\Controllers\Api\Auth\CustomerAuthController;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\FaqCategoryController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SearchController;
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
    Route::prefix('search')->group(function () {
        Route::get('/projects', [SearchController::class, 'searchProjects']);
        Route::get('/companies', [SearchController::class, 'searchCompanies']);
        Route::get('/projects/by-amount', [SearchController::class, 'searchProjectsByAmount']);
    });
    Route::prefix('/associations')->group(function () {
        Route::post('/register', [AssociationController::class, 'register']);
        Route::post('/verify-otp', [AssociationController::class, 'verifyOtp']);
        Route::post('/resend-otp', [AssociationAuthController::class, 'resendOtp']);
        Route::post('/login', [AssociationAuthController::class, 'resetPassword']);
        Route::post('/reset-password', [AssociationAuthController::class, 'resetPassword']);
        Route::post('/forgot-password', [AssociationAuthController::class, 'forgotPassword']);

    });
    Route::prefix('/customers')->group(function () {
        Route::post('/login', [CustomerAuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/change-password', [CustomerAuthController::class, 'changePassword']);
            Route::post('/logout', [CustomerAuthController::class, 'logout']);
            Route::get('/profile', [CustomerAuthController::class, 'profile']);
        });
    });
    Route::post('/companies/register', [CompanyController::class, 'register']);
    Route::post('/campaigns/{campaign}/vote', [CampaignController::class, 'vote']);

    route::prefix('companies')->group(function () {
        Route::post('/register', [CompanyAuthController::class, 'register']);
        Route::post('/login', [CompanyAuthController::class, 'login']);
        Route::middleware(['auth:sanctum', 'company'])->group(function () {
            Route::post('/logout', [CompanyAuthController::class, 'logout']);
            Route::get('/me', [CompanyAuthController::class, 'me']);
            Route::put('/companies/preferences', [CompanyController::class, 'updatePreferences']);
            Route::post('/campaigns', [CampaignController::class, 'create']);
            Route::get('/campaigns/{campaign}/results', [CampaignController::class, 'results']);
        });
    });

    Route::middleware(['auth:sanctum', 'association'])->group(function () {
        Route::apiResource('projects', ProjectController::class);

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
            Route::apiResource('faqs', FaqController::class);
            Route::apiResource('faq-categories', FaqCategoryController::class);
            Route::apiResource('customers', CustomerController::class);
            Route::apiResource('contacts', ContactController::class);
            Route::post('contacts/import', [ContactController::class, 'import']);
        });
    });
    Route::apiResource('activities', ActivityController::class)
        ->only(['index', 'store']);
});