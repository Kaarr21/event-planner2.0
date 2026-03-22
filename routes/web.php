<?php

use Illuminate\Support\Facades\Route;

Route::get('/', \App\Livewire\Home::class)->name('home');
Route::get('/about', \App\Livewire\About::class)->name('about');
Route::get('/contact', \App\Livewire\Contact::class)->name('contact');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/discover', \App\Livewire\Events\Discovery::class)->name('events.discovery');
Route::get('/events/{event}', \App\Livewire\Events\Show::class)->name('events.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/events', \App\Livewire\Events\Index::class)->name('events.index');
    Route::get('/events/cancelled', \App\Livewire\Events\Cancelled::class)->name('events.cancelled');
    Route::get('/events/create', \App\Livewire\Events\Create::class)->name('events.create');
    \Livewire\Volt\Volt::route('/events/{event}/checkout', 'events.checkout')->name('events.checkout');
    Route::get('/orders/{order}/status', [\App\Http\Controllers\MpesaController::class, 'checkStatus'])->name('mpesa.status');
    Route::get('/notifications', \App\Livewire\Notifications\Index::class)->name('notifications.index');
});

Route::get('/orgs/{organization:slug}', [\App\Http\Controllers\OrganizationController::class, 'show'])->name('organizations.show');

require __DIR__.'/auth.php';
