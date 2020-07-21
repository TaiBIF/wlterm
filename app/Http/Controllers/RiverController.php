<?php

namespace App\Http\Controllers;

use App\RiverBsTp;
use App\RiverHabitat;
use App\RiverMorphology;
use App\RiverSubstrate;
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
        $substrate = RiverSubstrate::all()->keyBy('substrate_id')->toArray();
        $habitateQuery = RiverHabitat::query();

        if ($date) {
            $habitateQuery->where('date', 'like', '%' . $date . '%');
        }

        if ($section) {
            $habitateQuery->where('section', $section);
        }

        $habitateRecords = $habitateQuery->paginate($this->perPage);
        $records = $habitateRecords->map(function ($item) use ($morphology, $substrate) {
            $item->morphology_name = $morphology[$item->morphology]['morphology_chinese'];
            $item->substrate_name = $substrate[$item->substrate]['substrate_chinese'];
            return $item;
        });

        return response()->json([
            'total' => $habitateRecords->total(),
            'currentPage' => $habitateRecords->currentPage(),
            'data' => $records,
        ]);
    }

    public function section(Request $request)
    {
        $date = $request->get('date');
        $section = $request->get('section');
        $river = $request->get('river');

        $sectionQuery = RiverSection::query();

        if ($date) {
            $sectionQuery->where('date', 'like', '%' . $date . '%');
        }

        if ($section) {
            $sectionQuery->where('section', $section);
        }

        if ($river) {
            $sectionQuery->where('river', 'like', '%' . $river . '%');
        }

        $habitateRecords = $sectionQuery->paginate($this->perPage);
        $records = $habitateRecords->map(function ($item) {
            return $item;
        });

        return response()->json([
            'total' => $habitateRecords->total(),
            'currentPage' => $habitateRecords->currentPage(),
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
