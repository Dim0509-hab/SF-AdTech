<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\RoleChange;


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

    // Определяем допустимые role_id: 1 = advertiser, 2 = webmaster
    $validRoleIds = [1, 2];
    $roleId = (int) $request->input('role_id');

    if (!in_array($roleId, $validRoleIds)) {
        return redirect()->back()->with('error', 'Некорректная роль');
    }

    // Обновляем только role_id — триггер сам подставит role
    $user->role_id = $roleId;
    $user->save();

    return redirect()
        ->route('admin.users')
        ->with('success', 'Роль пользователя обновлена');
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

         $users = User::where('role_id', '!=', 3) // исключаем админа (role_id = 3)
                ->paginate(20);

        return view('admin.users', compact('users'));
    }



   public function toggleActive(int $id)
{
    $user = User::findOrFail($id);

    $user->active = !$user->active;
    $user->save();

    // Логируем
    \Log::info("Статус пользователя #{$id} изменён на: " . ($user->active ? 'активен' : 'неактивен'), [
        'changed_by' => Auth::id(),
        'ip' => request()->ip(),
    ]);

    return redirect()->route('admin.users')->with('success', 'Статус пользователя обновлён.');
}


    public function destroy($id)
{
    $user = User::findOrFail($id);

    // Дополнительно можно проверить права:
    // if (!auth()->user()->isAdmin()) {
    //     return redirect()->back()->with('error', 'У вас нет прав.');
    // }

    $user->delete();

    return redirect()->route('admin.users')->with('success', 'Пользователь удалён.');
}
}
