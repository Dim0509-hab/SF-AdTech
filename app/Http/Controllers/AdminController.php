<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Offer;
use App\Models\Role;

/**
 * Контроллер для административной панели SF-AdTech.
 * @property \App\Models\User $user
 * Управление пользователями, офферами и системной статистикой.
 */
class AdminController extends Controller
{
    /**
     * AdminController constructor.
     *
     * Применяет middleware:
     *  - auth         => только авторизованные пользователи
     *  - role:admin   => только с ролью admin
     *
     * Разделение вызовов middleware помогает IDE корректно распознавать методы.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Главная страница админки.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $userCount = User::count();
        $offerCount = Offer::count();

        return view('admin.dashboard', compact('userCount', 'offerCount'));
    }

    /**
     * Список всех пользователей.
     *
     * @return \Illuminate\View\View
     */
    public function users()
    {
        $users = User::orderBy('id')->get();
        return view('admin.users', compact('users'));
    }

    /**
     * Список всех офферов с информацией о рекламодателях.
     *
     * @return \Illuminate\View\View
     */
    public function offers()
    {
        $offers = Offer::with('advertiser')->orderBy('id')->get();
        return view('admin.offers', compact('offers'));
    }

    /**
     * Включение/отключение пользователя (активен/не активен).
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleActive(int $id)
    {
        $user = User::findOrFail($id);
        $user->active = !$user->active;
        $user->save();

        return redirect()->back()->with('success', 'Статус пользователя обновлен.');
    }

    /**
     * Просмотр системной статистики (например, переходы и ошибки).
     *
     * @return \Illuminate\View\View
     */
    public function systemStats()
    {
        $offerCount = Offer::count();
        $userCount = User::count();

        return view('admin.stats', compact('offerCount', 'userCount'));
    }
}
