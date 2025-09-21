<?php
namespace RealSoft\FEL\Adapters\GT;

use RealSoft\FEL\Contracts\CountryAdapter;
use RealSoft\FEL\Domain\DTO\{Document,Cancellation};
use RuntimeException;

class GTAdapter implements CountryAdapter
{
    public function buildPayload(Document $doc): string
    {
        // TODO: map DTO -> XML (GT_Documento + complementos). Placeholder mínimo:
        $xml = new \DOMDocument('1.0','UTF-8');
        $root = $xml->createElement('GTDocumento');
        $xml->appendChild($root);
        $root->appendChild($xml->createElement('NumeroAcceso', $doc->meta['access_number'] ?? ''));
        return $xml->saveXML();
    }

    public function buildCancellation(Cancellation $c): string
    {
        $xml = new \DOMDocument('1.0','UTF-8');
        $root = $xml->createElement('GTAnulacion');
        $xml->appendChild($root);
        $root->appendChild($xml->createElement('UUID',$c->uuid));
        $root->appendChild($xml->createElement('Motivo',$c->reason));
        return $xml->saveXML();
    }

    public function normalizeResponse(array|string $raw): array
    {
        // Normalización mínima
        return is_array($raw) ? $raw : ['raw' => $raw];
    }
}
