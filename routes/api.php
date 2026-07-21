<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::apiResource('workspaces', WorkspaceController::class)->only(['index', 'store']);
    Route::patch('/workspaces/{workspace}/switch', [WorkspaceController::class, 'switchTo']);

    Route::get('/workspaces/{workspace}/projects', [ProjectController::class, 'index']);
    Route::post('/workspaces/{workspace}/projects', [ProjectController::class, 'store']);
    Route::get('/projects/{project}', [ProjectController::class, 'show']);
    Route::patch('/projects/{project}', [ProjectController::class, 'update']);
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);
    Route::post('/projects/{project}/ask-flow-ai', [ProjectController::class, 'askFlowAI']);

    Route::get('/projects/{project}/tasks', [TaskController::class, 'index']);
    Route::post('/projects/{project}/tasks', [TaskController::class, 'store']);
    Route::patch('/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
    Route::post('/tasks/{task}/comments', [TaskController::class, 'comment']);

    Route::get('/workspaces/{workspace}/team', [TeamController::class, 'index']);
    Route::get('/workspaces/{workspace}/team/workload', [TeamController::class, 'workload']);
    Route::post('/workspaces/{workspace}/team/invite', [TeamController::class, 'invite']);
});
