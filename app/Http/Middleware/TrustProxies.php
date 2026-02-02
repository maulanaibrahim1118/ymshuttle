<?php

namespace App\Http\Middleware;

use Fideloper\Proxy\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array|string
     */
    protected $proxies;

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_ALL;

    public function __construct()
    {
        $proxies = env('TRUSTED_PROXIES', null);

        if ($proxies && strpos($proxies, ',') !== false) {
            $this->proxies = array_map('trim', explode(',', $proxies));
        } else {
            $this->proxies = $proxies ?: null;
        }
    }
}