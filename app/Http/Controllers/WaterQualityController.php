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
        $locality = $request->get('locality_chinese');
        $date = $request->get('date');
        $id = $request->get('id');
        $sort = $request->get('sort');
        $direction = $request->get('direction', 'asc');

        $waterQualityQuery = WaterQuality::query()
            ->select(['id', 'date', 'record_id'])
            ->with('station:id,latitude,longitude,locality_chinese,maximum_elevation,maximum_depth')
            ->whereHas('station', function($query) use ($locality) {
                if ($locality) {
                    $query->where('locality_chinese', 'like', '%' . $locality . '%');
                }
            })
            ->where(function($query) {
                $query->where('project_id', 3)
                    ->orWhere('project_id', 17)
                    ->orWhere('project_id', 13)
                    ->orWhere('project_id', 4)
                    ->orWhere('project_id', 23);
            });

        if ($id) {
            $waterQualityQuery->where('id', $id);
        }

        if ($date) {
            $waterQualityQuery->where('date', 'like', '%' . $date . '%');
        }

        if ($sort && $direction) {
            $waterQualityQuery->orderBy($sort, $direction);
        } else {
            $waterQualityQuery->orderBy('id');
        }

        $waterQuality = $waterQualityQuery->paginate($this->perPage);

        $data = $waterQuality->map(function ($record) {
            $record->date = Carbon::createFromFormat('Y-m-d H:i:s', $record->date)->format('Y-m-d');
            $record->latitude = $record->station->latitude;
            $record->longitude = $record->station->longitude;
            $record->locality_chinese = $record->station->locality_chinese;
            $record->maximum_elevation = $record->station->maximum_elevation;
            $record->maximum_depth = $record->station->maximum_depth;
            unset($record->station);
            return $record;
        });

        return response()->json([
            'total' => $waterQuality->total(),
            'currentPage' => $waterQuality->currentPage(),
            'perPage' => $waterQuality->perPage(),
            'data' => $data,
        ]);
    }

    public function elementFlux(Request $request)
    {
        $locality = $request->get('locality_chinese');
        $stationId = $request->get('station_id');
        $date = $request->get('date');
        $sort = $request->get('sort');
        $direction = $request->get('direction', 'asc');

        $waterQualityQuery = WaterQuality::query()
            ->select(['water.id as water_id', 'station.id as station_id', 'date', 'latitude', 'longitude', 'locality_chinese', 'maximum_elevation', 'maximum_depth', 'record_id'])
            ->join('station', 'water.id', '=', 'station.id')
            ->where('project_id', 13);

        if ($locality) {
            $waterQualityQuery->where('station.locality_chinese', 'like', '%' . $locality . '%');
        }

        if ($stationId) {
            $waterQualityQuery->where('station.id', $stationId);
        }

        if ($date) {
            $waterQualityQuery->where('date',  'like', '%' . $date . '%');
        }

        if ($sort && $direction) {
            $waterQualityQuery->orderBy($sort, $direction);
        } else {
            $waterQualityQuery->orderBy('station_id');
        }


        $waterQuality = $waterQualityQuery->paginate($this->perPage);

        return response()->json([
            'total' => $waterQuality->total(),
            'current_page' => $waterQuality->currentPage(),
            'data' => $waterQuality->items(),
        ]);
    }
}
