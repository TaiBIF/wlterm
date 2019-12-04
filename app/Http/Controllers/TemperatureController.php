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
        $stationId = $request->get('station_id');
        $direction = $request->get('direction', 'asc');

        $recordsQuery = Temperature::query()
            ->select([
                'temperature.id',
                'date',
                'air',
                'water',
                'soil_0cm',
                'soil_25cm',
                'soil_50cm',
                'soil_65cm',
                'soil_90cm',
                'station.id as station_id',
                'station.latitude',
                'station.longitude',
                'station.locality_chinese',
                'station.maximum_elevation',
                'station.maximum_depth'])
            ->join('station', 'temperature.id', '=', 'station.id');

        if ($date) {
            $recordsQuery->where('date', 'like', '%' . $date . '%');
        }

        if ($locality) {
            $recordsQuery->where('station.locality_chinese', 'like', '%' . $locality . '%');
        }

        if ($stationId) {
            $recordsQuery->where('station.id', 'like', $stationId);
        }

        if ($sort && $direction) {
            $recordsQuery->orderBy($sort, $direction);
        } else {
            $recordsQuery->orderBy('station.id');
        }

        $records = $recordsQuery->paginate($this->perPage);

        return response()->json([
            'total' => $records->total(),
            'currentPage' => $records->currentPage(),
            'data' => $records->items(),
        ]);
    }
}
