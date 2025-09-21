<?php
namespace RealSoft\FEL\Domain\DTO;
class Cancellation {
  public function __construct(
    public string $uuid,
    public string $reason,
    public \DateTimeImmutable $date
  ) {}
}