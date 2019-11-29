<?php

namespace App\Http\Controllers;

use App\Station;
use Illuminate\Http\Request;

class StationController extends Controller
{
    protected $perPage = 50;

    public function index(Request $request)
    {
        $stationId = $request->get('auto_id');
        $localityName = $request->get('locality_chinese');
        $sort = $request->get('sort');
        $direction = $request->get('direction');

        $stationQuery = Station::query();

        if ($stationId) {
            $stationQuery->where('id', $stationId);
        }

        if ($localityName) {
            $stationQuery->where('locality_chinese', 'like', '%' . $localityName . '%');
        }

        if ($sort) {
            $stationQuery->orderBy($sort, $direction);
        }

        $stations = $stationQuery->paginate($this->perPage);

        return response()->json([
            'perPage' => $this->perPage,
            'total' => $stations->total(),
            'currentPage' => $stations->currentPage(),
            'data' => $stations->items(),
        ]);
    }

    public function map()
    {
        // SELECT *  FROM station   where (not latitude is null) and id < 14 order by latitude,longitude
        $stations = Station::query()
            ->select(['locality', 'locality_chinese', 'latitude', 'longitude'])
            ->whereNotNull('latitude')
            ->where('id', '<', 14)
            ->orderBy('latitude')
            ->orderBy('longitude')
            ->get();

        return response()
            ->json($stations);
    }
}
