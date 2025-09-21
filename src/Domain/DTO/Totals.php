<?php
namespace RealSoft\FEL\Domain\DTO;
class Totals {
  public function __construct(
    public float $subtotal,
    public float $tax,
    public float $discounts = 0.0,
    public float $grandTotal = 0.0,
    public ?string $currency = 'GTQ',
  ) {}
}