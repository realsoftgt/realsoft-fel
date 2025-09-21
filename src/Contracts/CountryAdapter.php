<?php
namespace RealSoft\FEL\Contracts;
use RealSoft\FEL\Domain\DTO\Document;
use RealSoft\FEL\Domain\DTO\Cancellation;
interface CountryAdapter {
  public function buildPayload(Document $doc): array|string;
  public function buildCancellation(Cancellation $c): array|string;
  public function normalizeResponse(array|string $raw): array;
}