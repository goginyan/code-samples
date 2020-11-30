<?php

namespace Notifications\DataTransferObjects\Responses;

use App\DataTransferObjects\Responses\ResponseDTO;
use App\Enums\HTTPResponse\HTTPStatusCodeEnum;
use Data\DataTransferObjects\Traits\ArrayableElements;
use Data\DataTransferObjects\Traits\Translatable;
use Illuminate\Contracts\Support\Arrayable;

class EmailPostmarkAppDomainVerificationResponseDTO extends ResponseDTO implements Arrayable
{
    use Translatable, ArrayableElements;

    /**
     * EmailPostmarkAppDomainVerificationResponseDTO constructor.
     */
    public function __construct()
    {
        $this->isError = false;
        $this->statusCode = HTTPStatusCodeEnum::SUCCESS_OK_200();

        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'data' => [
                'domain' => 'tryrush.io',
                'records' => [
                    [
                        'id' => 1,
                        'record_type' => 'TXT',
                        'name' => '20200521195117pm._domainkey',
                        'value' => 'k=rsa;p=MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCje8Mv7mpqxDtFzVN7JA3jZbMIQVB2dtobsy0OYO/d5Ib/zZxxzmso6zUUXKSjxd6U2weLmpcpTvLwy3WS40V2HtID4LHVXCreEP/SH3FGrOMXeLiLenv622q5V0n/zwX7Q6rY1GmTt9XzgObk+6DO',
                        'verification_status' => true,
                    ],
                ],
            ],
        ];
    }
}
