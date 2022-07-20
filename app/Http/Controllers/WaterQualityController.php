<?php

namespace App\Http\Controllers;

use App\Station;
use App\WaterQuality;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WaterQualityController extends Controller
{
    protected $perPage = 50;

    public function index(Request $request)
    {
        $locality = $request->get('locality_chinese');
        $date = $request->get('date');
        $id = $request->get('id');
        $collectorChinese = $request->get('collector_chinese');
        $sort = $request->get('sort');
        $direction = $request->get('direction', 'asc');

        $waterQualityQuery = WaterQuality::query()
            ->select(['water.*', 'date', 'record_id', 'station.locality_chinese'])
            ->join('station', 'station.id', '=', 'water.id')
            ->whereHas('station', function ($query) use ($locality) {
                if ($locality) {
                    $query->where('locality_chinese', 'like', '%' . $locality . '%');
                }
            })
            ->where(function ($query) {
                $query->where('project_id', 3)
                    ->orWhere('project_id', 17)
                    ->orWhere('project_id', 13)
                    ->orWhere('project_id', 4)
                    ->orWhere('project_id', 23);
            });

        if ($id) {
            $waterQualityQuery->where('water.id', $id);
        }

        if ($date) {
            $waterQualityQuery->where('date', 'like', '%' . $date . '%');
        }

        if ($collectorChinese) {
            $waterQualityQuery->where('collector_chinese', 'like', '%' . $collectorChinese . '%');
        }

        if ($sort && $direction) {
            $waterQualityQuery->orderBy($sort, $direction);
        } else {
            $waterQualityQuery->orderBy('id');
        }

        $waterQuality = $waterQualityQuery->paginate($this->perPage);

        $data = $waterQuality->map(function ($record) {
            $record->date = Carbon::createFromFormat('Y-m-d H:i:s', $record->date)->format('Y-m-d');
            $record->locality_chinese = $record->locality_chinese;
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
            $waterQualityQuery->where('date', 'like', '%' . $date . '%');
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

    public function report(Request $request)
    {
        $locality = $request->get('locality_chinese');
        $date = $request->get('date');
        $id = $request->get('id');
        $collectorChinese = $request->get('collector_chinese');
        $target = $request->get('target', 'PH');

        $waterQualityQuery = WaterQuality::query()
            ->select(['water.id', 'date', 'record_id', 'station.locality_chinese', "water.{$target}", 'station.id as station_id'])
            ->join('station', 'station.id', '=', 'water.id')
            ->whereHas('station', function ($query) use ($locality) {
                if ($locality) {
                    $query->where('locality_chinese', 'like', '%' . $locality . '%');
                }
            })
            ->where(function ($query) {
                $query->where('project_id', 3)
                    ->orWhere('project_id', 17)
                    ->orWhere('project_id', 13)
                    ->orWhere('project_id', 4)
                    ->orWhere('project_id', 23);
            });

        if ($id) {
            $waterQualityQuery->where('water.id', $id);
        }

        if ($date) {
            $waterQualityQuery->where('date', 'like', '%' . $date . '%');
        }

        if ($collectorChinese) {
            $waterQualityQuery->where('collector_chinese', 'like', '%' . $collectorChinese . '%');
        }

        $waterQuality = $waterQualityQuery
            ->where($target, '!=', '')
            ->orderBy('id')
            ->orderBy('date')->get();

        $stations = Station::select(['id', 'locality', 'locality_chinese'])
            ->whereIn('id', $waterQuality->unique('station_id')->pluck('station_id'))
            ->orderBy('station.latitude')
            ->orderBy('station.longitude')
            ->get()
            ->keyBy('id');

        $data = $waterQuality->groupBy('id')
            ->map(function ($records, $stationId) use ($target, $stations) {
                $st = $stations[$stationId];
                return [
                    'name' => "$st->locality_chinese (#$stationId)",
                    'data' => $records->map(function ($record) use ($target) {
                        return [
                            Carbon::createFromFormat('Y-m-d H:i:s', $record->date)->timestamp*1000,
                            $record->{$target},
                        ];
                    }),
                    'pointInterval' => 24 * 3600 * 1000,
                ];
            })->values();

        return response()->json([
            'data' => $data,
        ]);

    }
}
