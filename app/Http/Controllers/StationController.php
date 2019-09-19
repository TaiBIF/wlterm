<?php

namespace App\Http\Controllers;

use App\Station;
use Illuminate\Http\Request;

class StationController extends Controller
{
    protected $perPage = 50;

    public function index(Request $request)
    {
        $stationId = $request->get('station_id');
        $localityName = $request->get('locality_name');

        $stationQuery = Station::query();

        if ($stationId) {
            $stationQuery->where('id', $stationId);
        }

        if ($localityName) {
            $stationQuery->where('locality_chinese', 'like', '%' . $localityName . '%');
        }

        $stations = $stationQuery->paginate($this->perPage);

        return response()->json([
            'perPage' => $this->perPage,
            'total' => $stations->total(),
            'currentPage' => $stations->currentPage(),
            'data' => $stations->items(),
        ]);
    }
}
