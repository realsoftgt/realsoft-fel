<?php
namespace RealSoft\FEL\Contracts;
interface Transport {
  public function post(string $url, array $headers, array|string $body): array;
}