<?php

namespace App\Services;

use App\Http\Middleware\VerifyUserSessionAndIp;
use App\Models\Document;
use App\Repositories\DocumentRepository;
use App\Repositories\SecurityRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentService
{
    public function __construct(
        private DocumentRepository $repository,
        private SecurityRepository $security,
    ) {}

    public function list(?string $search = null): Collection
    {
        return $this->repository->all($search);
    }

    public function find(string $id): Document
    {
        return $this->repository->find($id);
    }

    public function create(array $payload, ?UploadedFile $file = null): Document
    {
        [$filePath, $metadata] = $this->storePdf($file, $payload['name']);

        if (($payload['support'] ?? 'Electrónico') === 'Electrónico' && ! $filePath) {
            throw ValidationException::withMessages([
                'file' => 'Debe adjuntar un PDF para soporte electrónico.',
            ]);
        }

        $document = $this->repository->create([
            'proceedings_id' => $payload['proceedings_id'],
            'sub_proceeding_id' => $payload['sub_proceeding_id'] ?? null,
            'document_type' => $payload['document_type'],
            'name' => $payload['name'],
            'description' => $payload['description'] ?? '',
            'document_creation_date' => $payload['document_creation_date'] ?? now(),
            'support' => $payload['support'] ?? 'Electrónico',
            'file_path' => $filePath,
            'file_metadata' => $metadata,
            'state' => $payload['state'] ?? 'Abierto',
            'is_deleted' => false,
        ]);

        $this->audit($document, 'UPDATE');

        return $document;
    }

    public function update(string $id, array $payload, ?UploadedFile $file = null): Document
    {
        $document = $this->repository->find($id);
        $filePath = $document->file_path;
        $metadata = $document->file_metadata ?? [
            'size_bytes' => 0,
            'mime_type' => 'application/vnd.physical-record',
            'original_name' => $payload['name'],
        ];

        if ($file) {
            if ($filePath) {
                Storage::disk('private')->delete($filePath);
            }

            [$filePath, $metadata] = $this->storePdf($file, $payload['name']);
        }

        $document = $this->repository->update($document, [
            'proceedings_id' => $payload['proceedings_id'],
            'sub_proceeding_id' => $payload['sub_proceeding_id'] ?? $document->sub_proceeding_id,
            'document_type' => $payload['document_type'],
            'name' => $payload['name'],
            'description' => $payload['description'] ?? '',
            'document_creation_date' => $payload['document_creation_date'] ?? $document->document_creation_date,
            'support' => $payload['support'] ?? $document->support,
            'file_path' => $filePath,
            'file_metadata' => $metadata,
            'state' => $payload['state'] ?? $document->state,
        ]);

        $this->audit($document, 'UPDATE');

        return $document;
    }

    public function view(string $id): Document
    {
        $document = $this->repository->find($id);
        $this->audit($document, 'VIEW');

        return $document;
    }

    public function download(string $id): StreamedResponse
    {
        $document = $this->repository->find($id);

        if (! $document->file_path || ! Storage::disk('private')->exists($document->file_path)) {
            throw ValidationException::withMessages([
                'file' => 'El documento es físico o no tiene PDF asociado.',
            ]);
        }

        $this->audit($document, 'DOWNLOAD');

        return Storage::disk('private')->download(
            $document->file_path,
            data_get($document->file_metadata, 'original_name', $document->name.'.pdf')
        );
    }

    public function inhabilitar(string $id): void
    {
        $this->repository->softDelete($this->repository->find($id));
    }

    /**
     * @return array{0: ?string, 1: array<string, mixed>}
     */
    private function storePdf(?UploadedFile $file, string $fallbackName): array
    {
        $metadata = [
            'size_bytes' => 0,
            'mime_type' => 'application/vnd.physical-record',
            'original_name' => $fallbackName,
        ];

        if (! $file) {
            return [null, $metadata];
        }

        if ($file->getSize() > 10 * 1024 * 1024) {
            throw ValidationException::withMessages([
                'file' => 'El PDF no puede superar 10 MB.',
            ]);
        }

        if ($file->getMimeType() !== 'application/pdf' && $file->getClientOriginalExtension() !== 'pdf') {
            throw ValidationException::withMessages([
                'file' => 'Solo se permiten archivos PDF (application/pdf).',
            ]);
        }

        $uuid = (string) Str::uuid();
        $filePath = $file->storeAs('documents', $uuid.'.pdf', 'private');
        $metadata = [
            'size_bytes' => $file->getSize(),
            'mime_type' => 'application/pdf',
            'original_name' => $file->getClientOriginalName(),
            'hash_sha256' => hash_file('sha256', $file->getRealPath()),
            'uuid' => $uuid,
        ];

        return [$filePath, $metadata];
    }

    private function audit(Document $document, string $action): void
    {
        $user = request()->user();
        $this->security->recordDocumentAudit([
            'user_id' => $user?->getKey(),
            'document_id' => $document->getKey(),
            'action' => $action,
            'ip_address' => VerifyUserSessionAndIp::clientIp(request()),
        ]);
    }
}
