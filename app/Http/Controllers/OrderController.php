<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $perPage = 50;
    public function index(Request $request)
    {
        $phylum = $request->get('phylum');
        $phylumCName = $request->get('phylum_c_name');
        $class = $request->get('class');
        $classCName = $request->get('class_c_name');
        $kingdomCName = $request->get('kingdom_c_name');
        $kingdomName = $request->get('kingdom');

        $orderQuery = Order::query();

        if ($phylum) {
            $orderQuery->where('phylum', 'like', '%' . $phylum . '%');
        }

        if ($phylumCName) {
            $orderQuery->where('phylum_c', 'like', '%' . $phylumCName . '%');
        }

        if ($kingdomCName) {
            $orderQuery->where('kingdom_c', 'like', '%' . $kingdomCName . '%');
        }

        if ($kingdomName) {
            $orderQuery->where('kingdom', 'like', '%' . $kingdomName . '%');
        }

        if ($class) {
            $orderQuery->where('class', 'like', '%' . $class . '%');
        }

        if ($classCName) {
            $orderQuery->where('class_c', 'like', '%' . $classCName . '%');
        }

        $orderPage = $orderQuery->paginate($this->perPage);
        return response()->json([
            'total' => $orderPage->total(),
            'current_page' => $orderPage->currentPage(),
            'data' => $orderPage->items(),
        ]);
    }
}
