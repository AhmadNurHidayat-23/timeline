<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\TimelineController;
use App\Http\Middleware\LocalManagement;
use Illuminate\Support\Facades\Route;

Route::get('/s/{token}', [TimelineController::class, 'shared'])
    ->where('token', '[A-Za-z0-9]{64}')->name('timelines.shared');

Route::middleware(LocalManagement::class)->scopeBindings()->group(function () {
    Route::get('/', [TimelineController::class, 'index'])->name('timelines.index');
    Route::resource('timelines', TimelineController::class)->except('index');
    Route::patch('/timelines/{timeline}/sort', [TimelineController::class, 'sort'])->name('timelines.sort');
    Route::put('/timelines/{timeline}/order', [EventController::class, 'reorder'])->name('events.reorder');
    Route::resource('timelines.events', EventController::class)->only(['store', 'edit', 'update', 'destroy']);
});
