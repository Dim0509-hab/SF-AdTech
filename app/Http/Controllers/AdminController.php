<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Offer;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

/**
 * Контроллер для административной панели SF-AdTech.
 * Управление пользователями, офферами и системной статистикой.
 */
class AdminController extends Controller
{
    protected function authorizeUser()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Доступ запрещён');
        }
    }

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->authorizeUser();
            return $next($request);
        });
    }

    /**
     * Главная страница админки.
     */
        public function dashboard()
    {
        $userCount = User::count();
        $offerCount = Offer::count();
        $pendingCount = User::where('status', 'pending')
            ->whereIn('role', ['advertiser', 'webmaster'])
            ->count();

        return view('admin.dashboard', compact('userCount', 'offerCount', 'pendingCount'));
    }

    /**
     * Список всех пользователей.
     */
    public function users()
    {
        $users = User::orderBy('id')->get();
        return view('admin.users', compact('users'));
    }

    /**
     * 🆕 Список пользователей на модерации (новые рекламодатели и веб-мастеры)
     */
    public function pendingUsers()
    {
        $pendingUsers = User::where('status', 'pending')->orderBy('created_at')->get();
        return view('admin.pending', compact('pendingUsers'));
    }

    /**
     * 🆕 Одобрить пользователя (авторизовать на работу)
     */
    public function approveUser($id)
    {
        $user = User::findOrFail($id);

        if (!in_array($user->role, ['advertiser', 'webmaster'])) {
            return redirect()->back()->with('error', 'Можно одобрять только рекламодателей и веб-мастеров.');
        }

        $user->update(['status' => 'approved']);

        return redirect()->back()->with('success', "✅ Пользователь «{$user->name}» одобрен и может начать работу.");
    }

    /**
     * 🆕 Отклонить пользователя
     */
    public function rejectUser($id)
    {
        $user = User::findOrFail($id);

        if (!in_array($user->role, ['advertiser', 'webmaster'])) {
            return redirect()->back()->with('error', 'Можно отклонять только рекламодателей и веб-мастеров.');
        }

        $user->update(['status' => 'rejected']);

        return redirect()->back()->with('info', "❌ Пользователь «{$user->name}» отклонён.");
    }

    /**
     * Список всех офферов с информацией о рекламодателях.
     */
    public function offers()
    {
        $offers = Offer::with('advertiser')->orderBy('id')->get();
        return view('admin.offers', compact('offers'));
    }

    /**
     * Включение/отключение пользователя (активен/не активен).
     */
    public function toggleActive(int $id)
    {
        $user = User::findOrFail($id);
        $user->active = !$user->active;
        $user->save();

        return redirect()->back()->with('success', 'Статус пользователя обновлен.');
    }

    /**
     * Просмотр системной статистики.
     */
    public function systemStats()
    {
        $offerCount = Offer::count();
        $userCount = User::count();

        return view('admin.stats', compact('offerCount', 'userCount'));
    }
}
