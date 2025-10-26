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
    $r->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6'
    ]);

    // Создаем пользователя и сразу указываем роль
    $user = User::create([
        'name' => $r->name,
        'email' => $r->email,
        'password' => Hash::make($r->password),
        'active' => 1,
        'role' => 'webmaster' // Здесь указываем роль напрямую
    ]);

    Auth::login($user);
    return $this->afterLoginRedirect($user);
}


    public function login(Request $r)
    {
        $r->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($r->only('email', 'password'))) {
            $r->session()->regenerate();
            $user = Auth::user(); // Получаем авторизованного пользователя
            return $this->afterLoginRedirect($user); // Передаем пользователя
        }
        return back()->withErrors(['email' => 'Неправильные учетные данные'])->withInput();
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
        return redirect()->route('webmaster.offers.index')
            ->with('error', 'Роль не назначена');
    }

    // Приводим роль к нижнему регистру для единообразия
    $userRole = strtolower($userRole);

    // Проверяем значение роли и перенаправляем
    switch ($userRole) {
        case 'admin':
            return redirect()->route('admin.dashboard');
            break;
        case 'advertiser':
            return redirect()->route('advertiser.offers.index');
            break;
        default:
            return redirect()->route('webmaster.offers.index');
            break;
    }
}

}
