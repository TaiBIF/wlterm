<?php

namespace App\Http\Controllers;

use App\AnimalClass;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    protected $perPage = 50;

    public function index(Request $request)
    {
        $phylum = $request->get('phylum');
        $phylumCName = $request->get('phylum_c');
        $class = $request->get('class');
        $classCName = $request->get('class_c');
        $kingdomCName = $request->get('kingdom_c');
        $kingdomName = $request->get('kingdom');

        $classQuery = AnimalClass::query();
        if ($phylum) {
            $classQuery->where('phylum', 'like', '%' . $phylum . '%');
        }

        if ($phylumCName) {
            $classQuery->where('phylum_c', 'like', '%' . $phylumCName . '%');
        }

        if ($kingdomCName) {
            $classQuery->where('kingdom_c', 'like', '%' . $kingdomCName . '%');
        }

        if ($kingdomName) {
            $classQuery->where('kingdom', 'like', '%' . $kingdomName . '%');
        }

        if ($class) {
            $classQuery->where('class', 'like', '%' . $class . '%');
        }

        if ($classCName) {
            $classQuery->where('class_c', 'like', '%' . $classCName . '%');
        }

        $classPage = $classQuery->paginate($this->perPage);
        return response()->json([
            'total' => $classPage->total(),
            'currentPage' => $classPage->currentPage(),
            'data' => $classPage->items(),
        ]);
    }
}
