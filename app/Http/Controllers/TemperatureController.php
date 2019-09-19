<?php

namespace App\Http\Controllers;

use App\Temperature;
use Illuminate\Http\Request;

class TemperatureController extends Controller
{
    protected $perPage = 50;

    public function index(Request $request)
    {
        $locality = $request->get('locality');
        $date = $request->get('date');
        $sort = $request->get('sort');
        $direction = $request->get('direction', 'asc');

        $recordsQuery = Temperature::query()
            ->select(['id', 'date', 'air', 'water', 'soil_0cm', 'soil_25cm', 'soil_50cm', 'soil_65cm', 'soil_90cm'])
            ->with('station:id,latitude,longitude,locality_chinese,maximum_elevation,maximum_depth')
            ->orderBy('record_id');

        if ($date) {
            $recordsQuery->where('date', 'like', '%' . $date . '%');
        }

        if ($locality) {
            $recordsQuery->where('station.locality_chinese', 'like', '%' . $locality . '%');
        }

        if ($sort && $direction) {
            $recordsQuery->orderBy($sort, $direction);
        }

        $records = $recordsQuery->paginate($this->perPage);

        return response()->json([
            'total' => $records->total(),
            'currentPage' => $records->currentPage(),
            'data' => $records->items(),
        ]);
    }
}
