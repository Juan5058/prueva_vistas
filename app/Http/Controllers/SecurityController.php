<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SecurityController extends Controller
{
    public function simulateIp(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'simulated_ip' => ['required', 'string', 'max:45'],
        ]);

        $request->session()->put('simulated_ip', $data['simulated_ip']);

        return back()->with('status', 'IP simulada aplicada: '.$data['simulated_ip']);
    }
}
