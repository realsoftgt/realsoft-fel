<?php
namespace RealSoft\FEL\Validators;

use RealSoft\FEL\Domain\DTO\Document;

class Registry
{
    public function __construct(protected string $schemasPath) {}

    public function validatorForCountry(string $country): ValidatorInterface
    {
        return match(strtoupper($country)) {
            'SV' => new SVJsonValidator($this->schemasPath.'/SV'),
            default => new GTXmlValidator($this->schemasPath.'/GT'),
        };
    }
}

interface ValidatorInterface {
    public function validate(array|string $payload, Document $doc): void;
}

class ValidatorException extends \RuntimeException
{
    public function __construct(public array $errors) {
        parent::__construct('Validation failed');
    }
}
