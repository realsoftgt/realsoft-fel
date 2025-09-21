<?php

namespace RealSoft\FEL;

use RealSoft\FEL\Contracts\CountryAdapter;
use RealSoft\FEL\Contracts\CertifierDriver;
use RealSoft\FEL\Domain\DTO\Document;
use RealSoft\FEL\Domain\DTO\Cancellation;
use RealSoft\FEL\Validators\ValidatorException;
use RealSoft\FEL\Validators\Registry as SchemaRegistry;

class FELManager
{
    public function __construct(
        protected CountryAdapter $adapter,
        protected CertifierDriver $driver,
        protected ?object $signer = null,
        protected ?SchemaRegistry $schemas = null,
    ){}

    public function issue(Document $doc): array
    {
        $payload = $this->adapter->buildPayload($doc);

        // Validación de estructura + reglas
        if ($this->schemas) {
            $this->schemas->validatorForCountry($doc->meta['country'] ?? 'GT')
                ->validate($payload, $doc);
        }

        $signed = $this->signer ? $this->signer->sign($payload, []) : $payload;
        $resp = $this->driver->certify($signed, ['doc_type' => $doc::class]);
        return $this->adapter->normalizeResponse($resp);
    }

    public function cancel(Cancellation $c): array
    {
        $payload = $this->adapter->buildCancellation($c);
        $signed = $this->signer ? $this->signer->sign($payload, []) : $payload;
        $resp = $this->driver->cancel($signed);
        return $this->adapter->normalizeResponse($resp);
    }
}
