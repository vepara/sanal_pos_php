<?php

namespace App\Requests\Common;

use Illuminate\Support\Facades\Config;

class GetTokenRequest
{
    /**
     * @var string
     */
    private string $app_secret;

    /**
     * @var string
     */
    private string $app_id;

    /**
     * @return string
     */
    public function getAppSecret(): string
    {
        return $this->app_secret;
    }

    /**
     * @param string $app_secret
     */
    public function setAppSecret(string $app_secret): void
    {
        $this->app_secret = $app_secret;
    }

    /**
     * @return string
     */
    public function getAppId(): string
    {
        return $this->app_id;
    }

    /**
     * @param string $app_id
     */
    public function setAppId(string $app_id): void
    {
        $this->app_id = $app_id;
    }

    public function getTokenData(): string
    {
        $data = [
            'app_secret' => $this->getAppSecret(),
            'app_id' => $this->getAppId(),
        ];

        return json_encode($data);
    }


}
