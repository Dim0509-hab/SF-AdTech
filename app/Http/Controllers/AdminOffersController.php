<?php
namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;

class AdminOffersController extends Controller
{
    public function index(Request $request)
    {
        $query = Offer::with('advertiser');

        if ($request->has('search')) {
            $query->whereHas('advertiser', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $offers = $query->paginate(20);

        return view('admin.offers', compact('offers'));
    }
}
