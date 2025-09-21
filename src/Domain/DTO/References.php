<?php
namespace RealSoft\FEL\Domain\DTO;
class References {
  public function __construct(
    public ?string $originalUuid = null,
    public ?string $reason = null,
  ) {}
}