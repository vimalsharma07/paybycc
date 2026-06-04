<?php

namespace App\Http\Controllers;

use App\Services\Orders\SellerSearch;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    public function index(Request $request, SellerSearch $search): View
    {
        $q = $request->string('q')->trim()->toString();
        $q = $q === '' ? null : $q;

        return view('marketplace.index', [
            'query' => $q,
            'freelancers' => $search->paginate($q),
        ]);
    }

    public function show(int $freelancer, SellerSearch $search): View
    {
        $seller = $search->findSeller($freelancer);
        abort_unless($seller !== null, 404);

        return view('marketplace.show', [
            'seller' => $seller,
        ]);
    }
}
