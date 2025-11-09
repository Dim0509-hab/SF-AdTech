<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Offer;
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
     * Список всех пользователей (с пагинацией).
     */
    public function users()
    {
        $users = User::orderBy('id')->paginate(20);
        return view('admin.users', compact('users'));
    }
    /**
     * Список пользователей на модерации (по дате создания).
     */
    public function pendingUsers()
    {
        $pendingUsers = User::where('status', 'pending')
            ->whereIn('role', ['advertiser', 'webmaster'])
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return view('admin.pending', compact('pendingUsers'));
    }
    /**
     * Одобрить пользователя.
     */
    public function approveUser($id)
    {
        $user = User::findOrFail($id);

        if (!in_array($user->role, ['advertiser', 'webmaster'])) {
            return redirect()->back()->with('error', 'Можно одобрять только рекламодателей и веб-мастеров.');
        }

        if ($user->status !== 'pending') {
            return redirect()->back()->with('info', 'Этот пользователь уже имеет статус: ' . $user->status);
        }

        $user->update(['status' => 'approved']);

        return redirect()->back()->with('success', "✅ Пользователь «{$user->name}» одобрен и может начать работу.");
    }

    /**
     * Отклонить пользователя.
     */
    public function rejectUser($id)
    {
        $user = User::findOrFail($id);

        if (!in_array($user->role, ['advertiser', 'webmaster'])) {
            return redirect()->back()->with('error', 'Можно отклонять только рекламодателей и веб-мастеров.');
        }

        if ($user->status !== 'pending') {
            return redirect()->back()->with('info', 'Этот пользователь уже имеет статус: ' . $user->status);
        }

        $user->update(['status' => 'rejected']);

        return redirect()->back()->with('info', "❌ Пользователь «{$user->name}» отклонён.");
    }
    /**
     * Переключить активность пользователя (с защитой от self-block).
     */
    public function toggleActive(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Вы не можете заблокировать сами себя.');
        }

        $user->active = !$user->active;
        $user->save();

        $status = $user->active ? 'активирован' : 'заблокирован';
        return redirect()->back()->with('success', "Пользователь «{$user->name}» {$status}.");
    }
    /**
     * Список всех офферов.
     */
    public function offers()
    {
        $offers = Offer::with('advertiser')->orderBy('id')->get();
        return view('admin.offers', compact('offers'));
    }

}
