<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;
use App\Models\Role;


class TrustProxies extends Middleware
{
    /**
     * Прокси, которым приложение доверяет.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    /**
     * Заголовки, используемые для определения IP клиента.
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR |
                         Request::HEADER_X_FORWARDED_HOST |
                         Request::HEADER_X_FORWARDED_PORT |
                         Request::HEADER_X_FORWARDED_PROTO |
                         Request::HEADER_X_FORWARDED_AWS_ELB;
}
