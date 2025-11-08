<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $r)
    {
        $validated = $r->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:advertiser,webmaster',
        ]);

        $role = Role::where('name', $validated['role'])->firstOrFail();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'active' => 1,
            'role' => $validated['role'],
            'role_id' => $role->id,
            'status' => 'pending', // 🔥 Добавлено: статус по умолчанию
        ]);

        // ❌ Не входить автоматически!
        // Auth::login($user); ← УДАЛИТЬ

        return redirect()->route('login')->with('message', 'Регистрация успешна Ваш аккаунт ожидает одобрения администратором.');
    }


    public function login(Request $r)
    {
        $r->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $r->email)->first();

        // 🔥 Проверка: существует ли пользователь и одобрен ли
        if (!$user || !Hash::check($r->password, $user->password)) {
            return back()->withErrors(['email' => 'Неправильные учётные данные'])->withInput();
        }

        // 🔥 Проверка статуса
        if ($user->status !== 'approved') {
            return back()->withErrors([
                'email' => 'Ваш аккаунт ожидает одобрения администратором. Пожалуйста, подождите уведомления.'
            ])->withInput();
        }

        // ✅ Всё ок — вход разрешён
        Auth::login($user);
        $r->session()->regenerate();

        return $this->afterLoginRedirect($user);
    }


    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect('/');
    }

    public function afterLoginRedirect($user)
{
    // Получаем роль пользователя
    $userRole = $user->role;

    // Проверяем, существует ли роль
    if (is_null($userRole)) {
        return redirect()->route('webmaster.offers')
            ->with('error', 'Роль не назначена');
    }

    // Приводим роль к нижнему регистру для единообразия
    $userRole = strtolower($userRole);

    // Проверяем значение роли и перенаправляем
    switch ($userRole) {
        case 'admin':
            return redirect()->route('admin.dashboard');

        case 'advertiser':
            return redirect()->route('advertiser.index');

        default:
    return redirect()->route('webmaster.offers');

    }
    }

}
