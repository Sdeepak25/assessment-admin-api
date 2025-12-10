<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TenantController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\TestSectionController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\ChoiceController;
use App\Http\Controllers\Api\CandidateController;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\AttemptController;
use App\Http\Controllers\Api\GradingController;
use App\Http\Controllers\Api\PsychometricModelController;
use App\Http\Controllers\Api\ProctoringController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\NotificationTemplateController;
use App\Http\Controllers\Api\IntegrationController;
use App\Http\Controllers\Api\BillingController;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\SettingsController;

Route::prefix('auth')->group(function () {
    Route::post('login',  [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('me',      [AuthController::class, 'me'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {

    // Tenants (super admin)
    Route::apiResource('tenants', TenantController::class);

    // Users & Roles
    Route::apiResource('users', UserController::class);
    Route::post('users/{id}/reset-password', [UserController::class, 'resetPassword']);
    Route::get('users', [UserController::class, 'index']);
    Route::post('users', [UserController::class, 'store']);
    Route::get('users/{user}', [UserController::class, 'show']);
    Route::put('users/{user}', [UserController::class, 'update']);
    Route::delete('users/{user}', [UserController::class, 'destroy']);
    Route::post('users/{user}/reset-password', [UserController::class, 'sendResetPassword']);

    Route::apiResource('roles', RoleController::class);
    Route::get('permissions', [PermissionController::class, 'index']);
    Route::put('roles/{id}/permissions', [RoleController::class, 'updatePermissions']);
    Route::get('permissions', [RoleController::class, 'listPermissions']);

    Route::get('/roles', [RoleController::class,'index']);
    Route::post('/roles', [RoleController::class,'store']);
    Route::get('/roles/{id}', [RoleController::class,'show']);
    Route::put('/roles/{id}', [RoleController::class,'update']);
    Route::delete('/roles/{id}', [RoleController::class,'destroy']);
    Route::post('roles/{id}/permissions', [RoleController::class, 'assignPermissions']);

    // Tests & Sections
    Route::apiResource('tests', TestController::class);
    Route::post('tests/{id}/publish',  [TestController::class, 'publish']);
    Route::post('tests/{id}/validate', [TestController::class, 'validateTest']);
    Route::post('tests/{id}/clone',    [TestController::class, 'cloneTest']);

    Route::post('tests/{id}/sections', [TestSectionController::class, 'store']);
    Route::put('sections/{id}',        [TestSectionController::class, 'update']);
    Route::delete('sections/{id}',     [TestSectionController::class, 'destroy']);

    // Item Bank
    Route::apiResource('items', ItemController::class);
    Route::post('items/bulk-import', [ItemController::class, 'bulkImport']);

    Route::get('items/{id}/choices',  [ChoiceController::class, 'indexByItem']);
    Route::post('items/{id}/choices', [ChoiceController::class, 'store']);
    Route::put('choices/{id}',        [ChoiceController::class, 'update']);
    Route::delete('choices/{id}',     [ChoiceController::class, 'destroy']);

    // Candidates & Assignments & Attempts
    Route::apiResource('candidates', CandidateController::class);
    Route::apiResource('assignments', AssignmentController::class)->only(['index', 'store', 'show']);

    Route::post('assignments/{id}/attempts', [AttemptController::class, 'start']);
    Route::get('attempts/{id}',              [AttemptController::class, 'show']);
    Route::post('attempts/{id}/submit',      [AttemptController::class, 'submit']);

    // Manual grading
    Route::get('grading/queue',                 [GradingController::class, 'queue']);
    Route::get('grading/responses/{id}',        [GradingController::class, 'show']);
    Route::post('grading/responses/{id}/grade', [GradingController::class, 'grade']);

    // Psychometrics
    Route::apiResource('psychometrics/models', PsychometricModelController::class);

    // Optional extra psychometric routes:
    Route::post('psychometrics/models/{id}/scales',              [PsychometricModelController::class, 'addScale']);
    Route::put('psychometrics/scales/{id}',                      [PsychometricModelController::class, 'updateScale']);
    Route::delete('psychometrics/scales/{id}',                   [PsychometricModelController::class, 'deleteScale']);
    Route::post('psychometrics/models/{id}/norm-groups',         [PsychometricModelController::class, 'addNormGroup']);
    Route::put('psychometrics/norm-groups/{id}',                 [PsychometricModelController::class, 'updateNormGroup']);
    Route::delete('psychometrics/norm-groups/{id}',              [PsychometricModelController::class, 'deleteNormGroup']);
    Route::post('psychometrics/scales/{scaleId}/items',          [PsychometricModelController::class, 'assignItemToScale']);
    Route::delete('psychometrics/scales/{scaleId}/items/{itemId}',[PsychometricModelController::class, 'removeItemFromScale']);

    // Proctoring
    Route::get('proctoring/sessions',               [ProctoringController::class, 'index']);
    Route::get('proctoring/sessions/{id}',          [ProctoringController::class, 'show']);
    Route::post('proctoring/sessions/{id}/flags',   [ProctoringController::class, 'addFlag']);
    Route::post('proctoring/sessions/{id}/messages',[ProctoringController::class, 'sendMessage']);
    Route::post('proctoring/sessions/{id}/status',  [ProctoringController::class, 'updateStatus']);

    // Reports
    Route::get('reports/summary',        [ReportController::class, 'summary']);
    Route::get('reports/tests/{testId}', [ReportController::class, 'testDetail']);
    Route::get('reports/export',         [ReportController::class, 'export']);

    // Notification Templates
    Route::apiResource('notification-templates', NotificationTemplateController::class);

    // Integrations
    Route::apiResource('integrations', IntegrationController::class)->only(['index','store','show','update','destroy']);

    // Billing
    Route::get('billing/summary',  [BillingController::class, 'summary']);
    Route::get('billing/invoices', [BillingController::class, 'invoices']);

    // Audit Logs
    Route::get('audit-logs', [AuditLogController::class, 'index']);

    // Settings
    Route::get('settings', [SettingsController::class, 'show']);
    Route::put('settings', [SettingsController::class, 'update']);
});
