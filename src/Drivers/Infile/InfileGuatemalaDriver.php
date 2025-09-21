<?php
namespace RealSoft\FEL\Drivers\Infile;

use RealSoft\FEL\Contracts\CertifierDriver;
use GuzzleHttp\Client;

class InfileGuatemalaDriver implements CertifierDriver
{
    protected Client $http;
    public function __construct()
    {
        $this->http = new Client(['timeout' => config('fel.providers.infile.gt.timeout',20)]);
    }

    public function certify(array|string $signedPayload, array $options = []): array
    {
        // TODO: POST to Infile GT endpoint with headers & API key.
        return ['status' => 'certified', 'uuid' => 'DUMMY-GT-UUID'];
    }

    public function query(string $externalId): array { return ['status'=>'certified']; }

    public function cancel(array|string $payload, array $options = []): array
    {
        return ['status' => 'cancelled'];
    }
}
