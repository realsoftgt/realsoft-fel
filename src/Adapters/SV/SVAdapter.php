<?php
namespace RealSoft\FEL\Adapters\SV;

use RealSoft\FEL\Contracts\CountryAdapter;
use RealSoft\FEL\Domain\DTO\{Document,Cancellation};

class SVAdapter implements CountryAdapter
{
    public function buildPayload(Document $doc): array
    {
        // TODO: map DTO -> JSON DTE
        return [
            'tipoDTE' => '01',
            'emisor' => ['nit' => $doc->issuer->taxId, 'nombre' => $doc->issuer->name],
            'receptor' => ['nit' => $doc->receiver->taxId, 'nombre' => $doc->receiver->name],
            'totales' => ['montoTotal' => $doc->totals->grandTotal],
        ];
    }

    public function buildCancellation(Cancellation $c): array
    {
        return ['uuid' => $c->uuid, 'motivo' => $c->reason];
    }

    public function normalizeResponse(array|string $raw): array
    {
        return is_array($raw) ? $raw : ['raw' => $raw];
    }
}
