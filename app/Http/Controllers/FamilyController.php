<?php

namespace App\Http\Controllers;

use App\Family;
use Illuminate\Http\Request;

class FamilyController extends Controller
{
    protected $perPage = 50;
    public function index(Request $request)
    {
        $family = $request->get('family');
        $familyCName = $request->get('family_c');
        $phylum = $request->get('phylum');
        $phylumCName = $request->get('phylum_c');
        $class = $request->get('class');
        $classCName = $request->get('class_c');
        $order = $request->get('order');
        $orderCName = $request->get('order_c');
        $kingdomCName = $request->get('kingdom_c');
        $kingdomName = $request->get('kingdom');

        $familyQuery = Family::query();

        if ($phylum) {
            $familyQuery->where('phylum', 'like', '%' . $phylum . '%');
        }

        if ($phylumCName) {
            $familyQuery->where('phylum_c', 'like', '%' . $phylumCName . '%');
        }

        if ($kingdomCName) {
            $familyQuery->where('kingdom_c', 'like', '%' . $kingdomCName . '%');
        }

        if ($kingdomName) {
            $familyQuery->where('kingdom', 'like', '%' . $kingdomName . '%');
        }

        if ($class) {
            $familyQuery->where('class', 'like', '%' . $class . '%');
        }

        if ($classCName) {
            $familyQuery->where('class_c', 'like', '%' . $classCName . '%');
        }

        if ($order) {
            $familyQuery->where('order', 'like', '%' . $order . '%');
        }

        if ($orderCName) {
            $familyQuery->where('order_c', 'like', '%' . $orderCName . '%');
        }

        if ($family) {
            $familyQuery->where('family', 'like', '%' . $family . '%');
        }

        if ($familyCName) {
            $familyQuery->where('family_c', 'like', '%' . $familyCName . '%');
        }

        if ($request->get('sort')) {
            $familyQuery->orderBy($request->get('sort'), $request->get('direction', 'asc'));
        } else {
            $familyQuery->orderBy('id');
        }

        $familyPage = $familyQuery->paginate($this->perPage);

        return response()->json([
            'total' => $familyPage->total(),
            'currentPage' => $familyPage->currentPage(),
            'data' => $familyPage->items(),
            'perPage' => $this->perPage,
        ]);
    }
}
