<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/events', \App\Livewire\Events\Index::class)->name('events.index');
    Route::get('/events/create', \App\Livewire\Events\Create::class)->name('events.create');
    Route::get('/events/{event}', \App\Livewire\Events\Show::class)->name('events.show');
    Route::get('/notifications', \App\Livewire\Notifications\Index::class)->name('notifications.index');
});

require __DIR__.'/auth.php';
