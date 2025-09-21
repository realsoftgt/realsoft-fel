<?php
namespace RealSoft\FEL\Validators;

use RealSoft\FEL\Domain\DTO\Document;

class GTXmlValidator implements ValidatorInterface
{
    public function __construct(protected string $path) {}

    public function validate(array|string $payload, Document $doc): void
    {
        if (!is_string($payload)) return;
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadXML($payload);
        $xsd = $this->path.'/xsd/GT_Documento.xsd';
        $ok = $dom->schemaValidate($xsd);
        if (!$ok) {
            $errs = array_map(fn($e)=>$e->message, libxml_get_errors());
            throw new ValidatorException($errs);
        }
    }
}
