<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/stripe', [WebhookController::class, 'handleWebhook'])->name('stripe.webhook');
