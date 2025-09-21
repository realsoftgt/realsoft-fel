<?php
namespace RealSoft\FEL\Domain\DTO;
class Tax {
  public function __construct(
    public string $code,
    public float $rate,
    public ?float $base = null,
    public ?float $amount = null,
  ) {}
}