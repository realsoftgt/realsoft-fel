<?php
namespace RealSoft\FEL\Contracts;
interface Signer {
  public function sign(array|string $payload, array $credentials): array|string;
}