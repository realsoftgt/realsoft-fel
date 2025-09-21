<?php
namespace RealSoft\FEL\Domain\Enums;
enum DocType: string
{
  case FACT = 'FACT'; // Factura normal
  case FCAM = 'FCAM'; // Factura cambiaria
  case FPEQ = 'FPEQ'; // Factura pequeño contribuyente
  case FCAP = 'FCAP'; // Factura cambiaria pequeño contribuyente
  case FESP = 'FESP'; // Factura especial
  case NABN = 'NABN'; // Nota de abono
  case NCRE = 'NCRE'; // Nota de crédito
  case NDEB = 'NDEB'; // Nota de débito
  case RDON = 'RDON'; // Recibo donación
  case RECI = 'RECI'; // Recibo
  case FACA = 'FACA'; // Factura contribuyente agropecuario
  case FCCA = 'FCCA'; // Factura cambiaria contribuyente agropecuario
  
  case CANCELLATION = 'CANCELLATION';
}
