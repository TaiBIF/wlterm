<?php

namespace App\Http\Controllers;

use App\RiverBsTp;
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

        $sectionQuery = RiverSection::query();

        if ($date) {
            $sectionQuery->where('date', 'like', '%' . $date . '%');
        }

        if ($section) {
            $sectionQuery->where('section', $section);
        }

        $habitateRecords = $sectionQuery->paginate($this->perPage);
        $records = $habitateRecords->map(function ($item) {
            return $item;
        });

        return response()->json([
            'total' => $habitateRecords->total(),
            'currentPage' => $records,
            'data' => $habitateRecords->items(),
        ]);
    }

    public function certainSection($id, $date)
    {
        $bsTp = RiverBsTp::where('bs_and_tp', $id)->where('date', $date)->first();
        $sections = RiverSection::where('bs_and_tp', $id)->where('date', $date)->get();

        return response()->json([
            'bs_tp' => $bsTp,
            'sections' => $sections->groupBy('section'),
        ]);
    }
}
