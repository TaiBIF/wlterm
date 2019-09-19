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
        $familyCName = $request->get('family_c_name');
        $phylum = $request->get('phylum');
        $class = $request->get('class');
        $classCName = $request->get('class_c_name');
        $kingdomCName = $request->get('kingdom_c_name');
        $kingdomName = $request->get('kingdom');

        $familyQuery = Family::query();

        if ($phylum) {
            $familyQuery->where(function($query) use ($phylum) {
                $query->where('phylum', 'like', '%' . $phylum . '%')
                    ->orWhere('phylum_c', 'like', '%' . $phylum . '%');
            });
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

        if ($family) {
            $familyQuery->where('family', 'like', '%' . $family . '%');
        }

        if ($familyCName) {
            $familyQuery->where('family_c', 'like', '%' . $familyCName . '%');
        }

        $familyPage = $familyQuery->orderBy('id')->paginate($this->perPage);

        return response()->json([
            'total' => $familyPage->total(),
            'currentPage' => $familyPage->currentPage(),
            'data' => $familyPage->items(),
            'perPage' => $this->perPage,
        ]);
    }
}
