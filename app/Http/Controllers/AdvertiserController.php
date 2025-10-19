<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offer;

class AdvertiserController extends Controller
{
    public function index()
    {
        return view('advertiser.dashboard');
    }

    public function offers()
    {
        $offers = Offer::where('advertiser_id', auth()->id())->get();
        return view('advertiser.offers.index', compact('offers'));
    }

    public function create()
    {
        return view('advertiser.offers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'target_url' => 'required|url',
        ]);

        Offer::create([
            'advertiser_id' => auth()->id(),
            'name' => $request->name,
            'price' => $request->price,
            'target_url' => $request->target_url,
            'themes' => $request->themes ?? [],
            'active' => true,
        ]);

        return redirect()->route('advertiser.offers')->with('success', 'Оффер создан!');
    }
}
