<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdvertiserController extends Controller
{
    /**
     * Метод для проверки прав доступа
     */
    protected function authorizeUser()
{
    if (Auth::user()->role !== 'advertiser') {
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
     * Главная страница рекламодателя
     */

public function index()
{
    $offers = Offer::all(); // или ваша логика получения офферов
    return view('advertiser.offers.index', ['offers' => $offers]);
}

    /**
     * Список офферов рекламодателя
     */
    public function offers()
    {
        $offers = Offer::where('advertiser_id', Auth::id())
            ->paginate(10);

        return view('advertiser.offers.index', compact('offers'));
    }

    /**
     * Форма создания оффера
     */
    public function create()
    {
        return view('advertiser.offers.create');
    }

    /**
     * Сохранение нового оффера
     */
    public function store(Request $request)
    {
        // Валидация входных данных
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0.01',
            'target_url' => 'required|url|active_url',
            'themes' => 'array|nullable'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            Offer::create([
                'advertiser_id' => Auth::id(),
                'name' => $request->name,
                'price' => $request->price,
                'target_url' => $request->target_url,
                'themes' => $request->themes ?? [],
                'active' => true,
            ]);

            return redirect()
                ->route('advertiser.offers.index')
                ->with('success', 'Оффер успешно создан!');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Произошла ошибка при создании оффера'])
                ->withInput();
        }
    }

    public function destroy(Request $request, $id)
{
    try {
        $offer = Offer::findOrFail($id);

        // Проверяем, что оффер принадлежит рекламодателю
        if ($offer->advertiser_id !== Auth::id()) {
            abort(403, 'Доступ запрещён');
        }

        // Можно использовать мягкое удаление
        $offer->delete();

        return redirect()
            ->route('advertiser.offers.index')
            ->with('success', 'Оффер успешно удалён!');
    } catch (\Exception $e) {
       return back()
    ->withErrors(['error' => 'Произошла ошибка при удалении оффера']) // массив в квадратных скобках
    ->withInput();

    }
}

}


