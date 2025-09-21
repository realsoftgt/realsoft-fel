<?php
use Illuminate\Support\Facades\Route;
use RealSoft\FEL\Http\Controllers\WebhooksController;

Route::post(config('fel.webhooks.route','/fel/webhooks'), [WebhooksController::class, 'handle'])
    ->name('fel.webhooks');
