<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function inventory(Request $request, ReportService $reports): Response
    {
        return $reports->downloadInventory($request->all());
    }
}

