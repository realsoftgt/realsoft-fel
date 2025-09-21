<?php
namespace RealSoft\FEL\Contracts;
interface CertifierDriver {
  public function certify(array|string $signedPayload, array $options = []): array;
  public function query(string $externalId): array;
  public function cancel(array|string $payload, array $options = []): array;
}