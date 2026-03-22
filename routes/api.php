<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MpesaController;

Route::post('/mpesa/callback', [MpesaController::class, 'handleCallback'])->name('mpesa.callback');
