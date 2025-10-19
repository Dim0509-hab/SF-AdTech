<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Offer;

class AdminController extends Controller
{
     public function __construct()
    {
        // Только для авторизованных пользователей с ролью admin
        $this->middleware(['auth', 'role:admin']);
    }

    // Главная страница админки
    public function index()
    {
        $userCount = User::count();
        $offerCount = Offer::count();
        return view('admin.dashboard', compact('userCount', 'offerCount'));
    }

    // Список всех пользователей
    public function users()
    {
        $users = User::orderBy('id')->get();
        return view('admin.users', compact('users'));
    }

    // Список всех офферов
    public function offers()
    {
        $offers = Offer::with('advertiser')->orderBy('id')->get();
        return view('admin.offers', compact('offers'));
    }
}
