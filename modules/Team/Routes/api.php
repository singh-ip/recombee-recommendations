<?php

use Modules\Team\Http\Controllers\TeamController;

Route::group(['prefix' => 'api/v1/mod/team', 'middleware' => ['auth:sanctum', 'verified']], function () {
    Route::group(['prefix' => '/teams'], function () {
        Route::get('/', [TeamController::class, 'index'])->can('list teams')->name('module.team.list_teams');
        Route::get('/{id}', [TeamController::class, 'show'])->name('module.team.show_team');
        Route::delete('/{id}', [TeamController::class, 'destroy'])->can('delete team')->name('module.team.delete_team');
        Route::put('/{id}', [TeamController::class, 'edit'])->can('edit team')->name('module.team.edit_team');
        Route::post('/', [TeamController::class, 'store'])->can('create team')->name('module.team.create_team');
        Route::post('/user-assignment', [TeamController::class, 'changeUserAssignment'])
            ->can('change team assignment')
            ->name('module.team.change_team_assignment');
        Route::post('/{id}/leave', [TeamController::class, 'leave'])->name('module.team.leave_team');
    });
});
