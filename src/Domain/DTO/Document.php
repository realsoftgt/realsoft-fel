<?php
namespace RealSoft\FEL\Domain\DTO;
abstract class Document {
  public function __construct(
    public string $internalId,
    public Party $issuer,
    public Party $receiver,
    public array $items,
    public Totals $totals,
    public \DateTimeImmutable $issueDate,
    public ?References $references = null,
    public array $meta = []
  ) {}
}