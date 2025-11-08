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

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|min:6',
            'role' => 'required|in:advertiser,webmaster',
        ]);

        $role = Role::where('name', $validated['role'])->firstOrFail();

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'active' => 1,
            'role' => $validated['role'],
            'role_id' => $role->id,
            'status' => 'pending',
        ]);

        return redirect()->route('login')->with('message', 'Регистрация успешна Ваш аккаунт ожидает одобрения администратором.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // Проверка: пользователь существует и пароль верный
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'Неправильные учётные данные.',
            ])->withInput();
        }

        // Проверка статуса
        if ($user->status !== 'approved') {
            return back()->withErrors([
                'email' => 'Ваш аккаунт ожидает одобрения администратором. Пожалуйста, подождите уведомления.',
            ])->withInput();
        }
        if (! $user->active) {
        return back()->withErrors([
            'email' => 'Ваш аккаунт временно заблокирован администратором. Обратитесь в поддержку.'
        ])->withInput();
        }

        // Вход разрешён
        Auth::login($user);
        $request->session()->regenerate();

        return $this->afterLoginRedirect($user);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    protected function afterLoginRedirect($user)
    {
        if (! $user->role) {
            return redirect()->route('webmaster.offers')->with('error', 'Роль не назначена.');
        }

        $role = strtolower($user->role);

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($role === 'advertiser') {
            return redirect()->route('advertiser.index');
        }

        return redirect()->route('webmaster.offers');
    }
}
