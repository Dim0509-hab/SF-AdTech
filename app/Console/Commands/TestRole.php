<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Middleware\EnsureRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TestRole extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:role {userId} {role}';

    /**
     * The console command description.
     */
    protected $description = 'Проверка работы middleware role для конкретного пользователя';

    public function handle()
    {
        $userId = $this->argument('userId');
        $role = $this->argument('role');

        $user = User::find($userId);

        if (!$user) {
            $this->error("Пользователь с ID {$userId} не найден!");
            return 1;
        }

        Auth::login($user); // Авторизуем пользователя

        $middleware = new EnsureRole();
        $request = Request::create('/dummy', 'GET'); // фиктивный запрос

        try {
            $middleware->handle($request, function($req){
                return "Middleware сработал успешно!";
            }, $role);

            $this->info("Пользователь {$user->name} с ролью '{$user->role}' прошёл проверку '{$role}'!");
        } catch (HttpException $e) {
            $this->error("Доступ запрещён: ".$e->getMessage());
        }

        return 0;
    }
}
