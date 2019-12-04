<?php

namespace App\Http\Controllers;

use App\RiverHabitat;
use App\RiverMorphology;
use App\RiverSection;
use Illuminate\Http\Request;

class RiverController extends Controller
{
    private $perPage = 50;

    public function habitat(Request $request)
    {
        $date = $request->get('date');
        $section = $request->get('section');

        $morphology = RiverMorphology::all()->keyBy('morphology_id')->toArray();
        $habitateQuery = RiverHabitat::query();

        if ($date) {
            $habitateQuery->where('date', 'like', '%' . $date . '%');
        }

        if ($section) {
            $habitateQuery->where('section', $section);
        }

        $habitateRecords = $habitateQuery->paginate($this->perPage);
        $records = $habitateRecords->map(function ($item) use ($morphology) {
            $item->morphology_name = $morphology[$item->morphology]['morphology_chinese'];
            return $item;
        });

        return response()->json([
            'total' => $habitateRecords->total(),
            'currentPage' => $records,
            'data' => $habitateRecords->items(),
        ]);
    }

    public function section(Request $request)
    {
        $date = $request->get('date');
        $section = $request->get('section');

        $morphology = RiverMorphology::all()->keyBy('morphology_id')->toArray();
        $sectionQuery = RiverSection::query();

        if ($date) {
            $sectionQuery->where('date', 'like', '%' . $date . '%');
        }

        if ($section) {
            $sectionQuery->where('section', $section);
        }

        $habitateRecords = $sectionQuery->paginate($this->perPage);
        $records = $habitateRecords->map(function ($item) use ($morphology) {
//            $item->morphology_name = $morphology[$item->morphology]['morphology_chinese'];
            return $item;
        });

        return response()->json([
            'total' => $habitateRecords->total(),
            'currentPage' => $records,
            'data' => $habitateRecords->items(),
        ]);
    }
}
