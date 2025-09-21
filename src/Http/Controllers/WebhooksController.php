<?php
namespace RealSoft\FEL\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class WebhooksController extends Controller
{
    public function handle(Request $request)
    {
        // TODO: verify signature with config('fel.webhooks.secret')
        // store payload & dispatch event
        return response()->json(['ok'=>true]);
    }
}
