<?php
namespace RealSoft\FEL\Validators;

use RealSoft\FEL\Domain\DTO\Document;
use Opis\JsonSchema\{Validator, ValidationResult, Errors\ErrorFormatter};

class SVJsonValidator implements ValidatorInterface
{
    public function __construct(protected string $path) {}

    public function validate(array|string $payload, Document $doc): void
    {
        if (!is_array($payload)) return;
        $schemaFile = $this->path.'/schema/factura.schema.json';
        $schema = json_decode(file_get_contents($schemaFile));
        $validator = new Validator();
        $result = $validator->schemaValidation((object)$payload, $schema);
        if (!$result->isValid()) {
            $formatter = new ErrorFormatter();
            $errors = $formatter->format($result->getErrors());
            throw new ValidatorException([$errors]);
        }
    }
}
