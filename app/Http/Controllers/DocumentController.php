<?php

namespace App\Http\Controllers;

use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function download(string $document, DocumentService $service): StreamedResponse|RedirectResponse
    {
        return $service->download($document);
    }
}
