<?php

namespace RealSoft\FEL\Adapters\GT;

use RealSoft\FEL\Contracts\CountryAdapter;
use RealSoft\FEL\Domain\DTO\{Document, Cancellation, Item, Tax, Invoice, CreditNote, DebitNote};
use RuntimeException;
use DOMDocument;
use DOMElement;

class GTAdapter implements CountryAdapter
{
    // IVA general Guatemala
    private const IVA_RATE = 0.12;

    public function buildPayload(Document $doc): string
    {
        // Regla CF > Q2,500 (configurable desde fel.php)
        $isCF = strtoupper($doc->receiver->taxId ?? 'CF') === 'CF';
        $limit = (float) config('fel.gt_policy.cf_max_amount', 2500.00);
        $requireIdOver = (bool) config('fel.gt_policy.require_id_over_cf_max', true);

        if ($isCF && $requireIdOver && $doc->totals->grandTotal > $limit) {
            throw new RuntimeException('Ventas a CF superiores a Q2,500 requieren NIT o DPI/CUI.');
        }

        // Datos de emisor/receptor/establecimiento
        $est = (string) ($doc->meta['establishment'] ?? '1');
        $afiliacionIVA = (string) ($doc->meta['emitter_vat_affiliation'] ?? 'GEN'); // SAT: GEN por defecto
        $moneda = (string) ($doc->totals->currency ?? 'GTQ');
        $correoEmisor = $doc->issuer->email ?? null;
        $correoReceptor = $doc->receiver->email ?? null;

        // Tipo FEL
        $tipo = $this->mapDocType($doc);

        // Construcción XML con namespaces oficiales FEL 0.2.0
        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = false;

        $dteNs  = 'http://www.sat.gob.gt/dte/fel/0.2.0';
        $dsNs   = 'http://www.w3.org/2000/09/xmldsig#';
        $xsiNs  = 'http://www.w3.org/2001/XMLSchema-instance';

        $GTDocumento = $xml->createElementNS($dteNs, 'dte:GTDocumento');
        $GTDocumento->setAttribute('Version', '0.1');
        $GTDocumento->setAttributeNS('http://www.w3.org/2000/xmlns/' , 'xmlns:ds', $dsNs);
        $GTDocumento->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', $xsiNs);
        // schemaLocation: coloca el valor real de tu XSD publicado/instalado
        // $GTDocumento->setAttributeNS($xsiNs, 'xsi:schemaLocation', 'http://www.sat.gob.gt/dte/fel/0.2.0 GT_Documento-0.2.0.xsd');
        $xml->appendChild($GTDocumento);

        $SAT = $xml->createElementNS($dteNs, 'dte:SAT');
        $SAT->setAttribute('ClaseDocumento', 'dte');
        $GTDocumento->appendChild($SAT);

        $DTE = $xml->createElementNS($dteNs, 'dte:DTE');
        $DTE->setAttribute('ID', 'DatosCertificados');
        $SAT->appendChild($DTE);

        $DatosEmision = $xml->createElementNS($dteNs, 'dte:DatosEmision');
        $DatosEmision->setAttribute('ID', 'DatosEmision');
        $DTE->appendChild($DatosEmision);

        // DatosGenerales
        $DatosGenerales = $xml->createElementNS($dteNs, 'dte:DatosGenerales');
        $DatosGenerales->setAttribute('CodigoMoneda', $moneda);
        $DatosGenerales->setAttribute('FechaHoraEmision', $doc->issueDate->format('Y-m-d\TH:i:sP')); // 2020-04-21T09:58:00-06:00
        $DatosGenerales->setAttribute('Tipo', $tipo); // FACT, NCRE, NDEB, etc.
        $DatosEmision->appendChild($DatosGenerales);

        // Emisor
        $Emisor = $xml->createElementNS($dteNs, 'dte:Emisor');
        $Emisor->setAttribute('AfiliacionIVA', $afiliacionIVA);
        $Emisor->setAttribute('CodigoEstablecimiento', $est);
        if ($correoEmisor) {
            $Emisor->setAttribute('CorreoEmisor', $correoEmisor);
        }
        $Emisor->setAttribute('NITEmisor', $this->sanitizeTaxId($doc->issuer->taxId));
        $Emisor->setAttribute('NombreComercial', $doc->meta['emitter_trade_name'] ?? ($doc->issuer->name));
        $Emisor->setAttribute('NombreEmisor', $doc->issuer->name);
        $DatosEmision->appendChild($Emisor);

        // Dirección Emisor (usa meta con fallback simples)
        $this->appendAddress(
            $xml,
            $Emisor,
            'dte:DireccionEmisor',
            $doc->meta['emitter_address'] ?? [
                'Direccion'     => 'CIUDAD',
                'CodigoPostal'  => '01001',
                'Municipio'     => 'GUATEMALA',
                'Departamento'  => 'GUATEMALA',
                'Pais'          => 'GT',
            ],
            $dteNs
        );

        // Receptor
        $Receptor = $xml->createElementNS($dteNs, 'dte:Receptor');
        if ($correoReceptor) {
            $Receptor->setAttribute('CorreoReceptor', $correoReceptor);
        }
        $Receptor->setAttribute('IDReceptor', $this->sanitizeTaxId($doc->receiver->taxId));
        $Receptor->setAttribute('NombreReceptor', $doc->receiver->name);
        $DatosEmision->appendChild($Receptor);

        $this->appendAddress(
            $xml,
            $Receptor,
            'dte:DireccionReceptor',
            $doc->meta['receiver_address'] ?? [
                'Direccion'     => 'CIUDAD',
                'CodigoPostal'  => '01001',
                'Municipio'     => 'GUATEMALA',
                'Departamento'  => 'GUATEMALA',
                'Pais'          => 'GT',
            ],
            $dteNs
        );

        // Frases (opcional). Ejemplo igual que tu XML
        if (!empty($doc->meta['frases']) && is_array($doc->meta['frases'])) {
            $Frases = $xml->createElementNS($dteNs, 'dte:Frases');
            foreach ($doc->meta['frases'] as $frase) {
                $Fr = $xml->createElementNS($dteNs, 'dte:Frase');
                $Fr->setAttribute('CodigoEscenario', (string)($frase['CodigoEscenario'] ?? '1'));
                $Fr->setAttribute('TipoFrase', (string)($frase['TipoFrase'] ?? '1'));
                $Frases->appendChild($Fr);
            }
            $DatosEmision->appendChild($Frases);
        }

        // Items
        $Items = $xml->createElementNS($dteNs, 'dte:Items');
        $line = 1;
        $totalImpuestos = 0.0;

        /** @var Item $it */
        foreach ($doc->items as $it) {
            $Item = $xml->createElementNS($dteNs, 'dte:Item');
            $Item->setAttribute('BienOServicio', $this->guessBOS($it));
            $Item->setAttribute('NumeroLinea', (string)$line);

            // Cantidad, Unidad, Descripción
            $this->appendText($xml, $Item, $dteNs, 'dte:Cantidad', $this->n2($it->quantity));
            $this->appendText($xml, $Item, $dteNs, 'dte:UnidadMedida', $it->unit ?? 'UND');
            $this->appendText($xml, $Item, $dteNs, 'dte:Descripcion', $it->description);

            // Precio unitario y Precio (línea)
            $linePrice = $it->price * $it->quantity; // asumimos price = precio con IVA (como tu ejemplo)
            $this->appendText($xml, $Item, $dteNs, 'dte:PrecioUnitario', $this->n2($it->price));
            $this->appendText($xml, $Item, $dteNs, 'dte:Precio', $this->n2($linePrice));

            // Descuento (si no hay, 0.00)
            $discount = (float)($it->discount ?? 0.0);
            $this->appendText($xml, $Item, $dteNs, 'dte:Descuento', $this->n2($discount));

            // Impuestos
            $Impuestos = $xml->createElementNS($dteNs, 'dte:Impuestos');

            // IVA general: base y monto
            [$base, $ivaAmount] = $this->computeIVAFromGross($linePrice - $discount, self::IVA_RATE);
            $totalImpuestos += $ivaAmount;

            $Imp = $xml->createElementNS($dteNs, 'dte:Impuesto');
            $this->appendText($xml, $Imp, $dteNs, 'dte:NombreCorto', 'IVA');
            $this->appendText($xml, $Imp, $dteNs, 'dte:CodigoUnidadGravable', '1'); // 1 = IVA
            $this->appendText($xml, $Imp, $dteNs, 'dte:MontoGravable', $this->n2($base));      // 107.14
            $this->appendText($xml, $Imp, $dteNs, 'dte:MontoImpuesto', $this->n2($ivaAmount)); // 12.86
            $Impuestos->appendChild($Imp);
            $Item->appendChild($Impuestos);

            // Total de la línea (con impuesto)
            $this->appendText($xml, $Item, $dteNs, 'dte:Total', $this->n2($linePrice - $discount));

            $Items->appendChild($Item);
            $line++;
        }

        $DatosEmision->appendChild($Items);

        // Totales
        $Totales = $xml->createElementNS($dteNs, 'dte:Totales');
        $TotalImpuestos = $xml->createElementNS($dteNs, 'dte:TotalImpuestos');
        $TotalImpuesto = $xml->createElementNS($dteNs, 'dte:TotalImpuesto');
        $TotalImpuesto->setAttribute('NombreCorto', 'IVA');
        $TotalImpuesto->setAttribute('TotalMontoImpuesto', $this->n2($totalImpuestos));
        $TotalImpuestos->appendChild($TotalImpuesto);
        $Totales->appendChild($TotalImpuestos);

        // Gran Total = doc->totals->grandTotal (preferimos el DTO para cuadrar con contabilidad)
        $this->appendText($xml, $Totales, $dteNs, 'dte:GranTotal', $this->n2($doc->totals->grandTotal));
        $DatosEmision->appendChild($Totales);

        // Adenda (opcional)
        if (!empty($doc->meta['adenda']) && is_array($doc->meta['adenda'])) {
            $Adenda = $xml->createElementNS($dteNs, 'dte:Adenda');
            foreach ($doc->meta['adenda'] as $k => $v) {
                // Elementos sin namespace específico, igual que tu ejemplo
                $node = $xml->createElement($k, htmlspecialchars((string)$v));
                $Adenda->appendChild($node);
            }
            $SAT->appendChild($Adenda);
        }

        return $xml->saveXML();
    }

    public function buildCancellation(Cancellation $c): string
    {
        $xml = new DOMDocument('1.0','UTF-8');
        $xml->formatOutput = false;

        $dteNs  = 'http://www.sat.gob.gt/dte/fel/0.1.0'; // la anulación usa namespace/version propios en implementaciones; ajusta según XSD que uses
        $GTAnulacion = $xml->createElementNS($dteNs, 'dte:GTAnulacion');
        $xml->appendChild($GTAnulacion);

        $SAT = $xml->createElementNS($dteNs, 'dte:SAT');
        $GTAnulacion->appendChild($SAT);

        $AnulacionDTE = $xml->createElementNS($dteNs, 'dte:AnulacionDTE');
        $SAT->appendChild($AnulacionDTE);

        $this->appendText($xml, $AnulacionDTE, $dteNs, 'dte:NumeroDocumentoAAnular', $c->uuid);
        $this->appendText($xml, $AnulacionDTE, $dteNs, 'dte:MotivoAnulacion', $c->reason);
        $this->appendText($xml, $AnulacionDTE, $dteNs, 'dte:FechaEmisionDocumentoAnular', $c->date->format('Y-m-d'));

        return $xml->saveXML();
    }

    public function normalizeResponse(array|string $raw): array
    {
        // Normalización mínima; ajusta cuando integres Infile (UUID, serie, timbre, enlaces XML/PDF)
        return is_array($raw) ? $raw : ['raw' => $raw];
    }

    private function appendAddress(DOMDocument $xml, DOMElement $parent, string $tag, array $data, string $ns): void
    {
        $addr = $xml->createElementNS($ns, $tag);
        $addr->appendChild($xml->createElementNS($ns, 'dte:Direccion',     $data['Direccion']    ?? 'CIUDAD'));
        $addr->appendChild($xml->createElementNS($ns, 'dte:CodigoPostal',  $data['CodigoPostal'] ?? '01001'));
        $addr->appendChild($xml->createElementNS($ns, 'dte:Municipio',     $data['Municipio']    ?? 'GUATEMALA'));
        $addr->appendChild($xml->createElementNS($ns, 'dte:Departamento',  $data['Departamento'] ?? 'GUATEMALA'));
        $addr->appendChild($xml->createElementNS($ns, 'dte:Pais',          $data['Pais']         ?? 'GT'));
        $parent->appendChild($addr);
    }

    private function appendText(DOMDocument $xml, DOMElement $parent, string $ns, string $tag, string $value): void
    {
        $parent->appendChild($xml->createElementNS($ns, $tag, $value));
    }

    private function n2(float $n): string
    {
        // 2 decimales con punto (requerido por FEL)
        return number_format($n, 2, '.', '');
    }

    private function computeIVAFromGross(float $gross, float $rate): array
    {
        // Si el precio de línea incluye IVA (como tu ejemplo 120.00), base = gross / (1+rate)
        $base = $gross / (1 + $rate);
        $tax  = $gross - $base;
        // Redondeo típico SAT a 2 decimales
        $base = round($base, 2);
        $tax  = round($tax, 2);
        return [$base, $tax];
    }

    private function guessBOS(Item $it): string
    {
        // Heurística simple (puedes sobreescribir con meta por línea)
        $desc = strtoupper($it->description ?? '');
        return str_contains($desc, 'SERV') ? 'S' : 'B';
    }

    private function mapDocType(Document $doc): string
    {
        return match (true) {
            $doc instanceof Invoice    => 'FACT',
            $doc instanceof CreditNote => 'NCRE',
            $doc instanceof DebitNote  => 'NDEB',
            default                    => 'FACT',
        };
    }

    private function sanitizeTaxId(string $id): string
    {
        // Permite NIT con K, DPI/CUI o CF; remueve espacios
        return preg_replace('/\s+/', '', strtoupper($id));
    }
}
