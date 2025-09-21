<?php
namespace RealSoft\FEL\Domain\DTO;
class Item {
  public function __construct(
    public string $sku,
    public string $description,
    public float $quantity,
    public float $price,
    public array $taxes = [],
    public ?float $discount = 0.0,
    public ?string $unit = 'UND',
  ) {}
}