<?php
namespace RealSoft\FEL\Console\Commands;

use Illuminate\Console\Command;

class CloseContingencyCommand extends Command
{
    protected $signature = 'fel:contingency:close {--est=*} {--channel=email}';
    protected $description = 'Cierra contingencia por establecimiento y genera reporte diario.';

    public function handle(): int
    {
        $this->info('Closing contingency and generating report...');
        // TODO: aggregate, CSV, email
        return self::SUCCESS;
    }
}
