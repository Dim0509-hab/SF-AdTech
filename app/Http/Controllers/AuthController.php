<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin(){ return view('auth.login'); }
    public function showRegister(){ return view('auth.register'); }

    public function register(Request $r){
        $r->validate(['name'=>'required','email'=>'required|email|unique:users,email','password'=>'required|min:6']);
        $user = User::create(['name'=>$r->name,'email'=>$r->email,'password'=>Hash::make($r->password),'role'=>'webmaster','active'=>1]);
        Auth::login($user);
        return $this->afterLoginRedirect();
    }

    public function login(Request $r){
        $r->validate(['email'=>'required|email','password'=>'required']);
        if (Auth::attempt($r->only('email','password'))){
            $r->session()->regenerate();
            return $this->afterLoginRedirect();
        }
        return back()->withErrors(['email'=>'Неправильные учетные данные'])->withInput();
    }

    public function logout(Request $r){
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect('/');
    }

    protected function afterLoginRedirect(){
        $user = Auth::user();
        if (!$user->active){ Auth::logout(); return redirect('/login')->withErrors(['email'=>'Аккаунт отключён']); }
        if ($user->role === 'admin') return redirect()->route('admin.dashboard');
        if ($user->role === 'advertiser') return redirect()->route('advertiser.offers.index');
        return redirect()->route('webmaster.offers.index');
    }
}
