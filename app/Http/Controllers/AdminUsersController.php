<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class AdminUsersController extends Controller
{
    protected function authorizeUser()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Доступ запрещён');
        }
    }
public function assignRole(Request $request, $id)
{
    $user = User::findOrFail($id);

    // Валидация роли (опционально)
    $validRoles = ['advertiser', 'affiliate'];
    if (!in_array($request->input('role'), $validRoles)) {
        return redirect()->back()->with('error', 'Некорректная роль');
    }

    $user->role = $request->input('role');
    $user->save();

    return redirect()
        ->route('admin.users')
        ->with('success', 'Роль обновлена');
}

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->authorizeUser();
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = User::query();

        // Поиск по email
        if ($request->has('search')) {
            $query->where('email', 'like', '%' . $request->search . '%');
        }

        $users = $query->paginate(20);

        return view('admin.users', compact('users'));
    }
    public function toggleStatus(Request $request, $id)
{
    // Логика переключения статуса
    $user = User::findOrFail($id);
    $user->active = !$user->active;
    $user->save();

    return redirect()->route('admin.users')->with('success', 'Статус изменён');
}


    public function toggleActive(int $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->active = !$user->active;
            $user->save();

            // Логирование действия
            \Illuminate\Support\Facades\Log::info("Пользователь {$id} изменён на статус: " . ($user->active ? 'активен' : 'неактивен'));


            return redirect()->route('admin.users')->with('success', 'Статус пользователя обновлён.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ошибка при обновлении статуса: ' . $e->getMessage());
        }
    }
}
