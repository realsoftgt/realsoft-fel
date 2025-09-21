<?php
namespace RealSoft\FEL\Domain\DTO;
class Party {
  public function __construct(
    public string $taxId,
    public string $name,
    public ?string $address = null,
    public ?string $email = null,
  ) {}
}