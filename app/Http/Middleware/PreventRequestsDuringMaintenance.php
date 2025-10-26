<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;
use App\Models\Role;

class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * Список URI, доступных во время режима обслуживания.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}
