<?php
namespace RealSoft\FEL\Drivers\Infile;

use RealSoft\FEL\Contracts\CertifierDriver;
use GuzzleHttp\Client;

class InfileElSalvadorDriver implements CertifierDriver
{
    protected Client $http;
    public function __construct()
    {
        $this->http = new Client(['timeout' => config('fel.providers.infile.sv.timeout',20)]);
    }

    public function certify(array|string $signedPayload, array $options = []): array
    {
        // TODO: POST to Infile SV endpoint.
        return ['status' => 'certified', 'acuse' => 'DUMMY-SV-SELLO'];
    }

    public function query(string $externalId): array { return ['status'=>'received']; }

    public function cancel(array|string $payload, array $options = []): array
    {
        return ['status' => 'cancelled'];
    }
}
