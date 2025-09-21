<?php
namespace RealSoft\FEL\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RetryContingencyDispatch implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(): void
    {
        // TODO: find contingency documents and resend with access_number
    }
}
