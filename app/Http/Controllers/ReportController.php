<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function inventory(ReportService $reports): Response
    {
        return $reports->downloadInventory();
    }
}
