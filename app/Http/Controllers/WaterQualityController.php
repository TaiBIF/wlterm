<?php

namespace App\Http\Controllers;

use App\Main;
use App\WaterQuality;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class WaterQualityController extends Controller
{
    protected $perPage = 50;

    public function index(Request $request)
    {
        $locality = $request->get('locality');
        $sort = $request->get('sort');
        $direction = $request->get('direction', 'asc');

        $waterQualityQuery = WaterQuality::query()
            ->select(['id', 'date', 'record_id'])
            ->with('station:id,latitude,longitude,locality_chinese,maximum_elevation,maximum_depth')
            ->where('project_id', 3)
            ->orWhere('project_id', 4);

        if ($locality) {
            $waterQualityQuery->where('station.locality_chinese', 'like', '%' . $locality . '%');
        }

        if ($sort && $direction) {
            $waterQualityQuery->orderBy($sort, $direction);
        }

        $waterQuality = $waterQualityQuery->paginate($this->perPage);

        $data = $waterQuality->map(function ($record) {
            $record->date = Carbon::createFromFormat('Y-m-d H:i:s', $record->date)->format('Y-m-d');
            return $record;
        });

        return response()->json([
            'total' => $waterQuality->total(),
            'current_page' => $waterQuality->currentPage(),
            'data' => $data,
        ]);
    }

    public function elementFlux(Request $request)
    {
        $locality = $request->get('locality');
        $sort = $request->get('sort');
        $direction = $request->get('direction', 'asc');

        $waterQualityQuery = WaterQuality::query()
            ->select(['water.id as water_id', 'station.id as station_id', 'date', 'latitude', 'longitude', 'locality_chinese', 'maximum_elevation', 'maximum_depth'])
            ->join('station', 'water.id', '=', 'station.id')
            ->where('project_id', 13)
            ->orderBy('station_id');

        if ($locality) {
            $waterQualityQuery->where('station.locality_chinese', 'like', '%' . $locality . '%');
        }

        if ($sort && $direction) {
            $waterQualityQuery->orderBy($sort, $direction);
        }

        $waterQuality = $waterQualityQuery->paginate($this->perPage);

        return response()->json([
            'total' => $waterQuality->total(),
            'current_page' => $waterQuality->currentPage(),
            'data' => $waterQuality->items(),
        ]);
    }
}
