<?php
namespace RealSoft\FEL\Domain\Enums;
enum DocType:string { case INVOICE='INVOICE'; case CREDIT_NOTE='CREDIT_NOTE'; case DEBIT_NOTE='DEBIT_NOTE'; case CANCELLATION='CANCELLATION'; }