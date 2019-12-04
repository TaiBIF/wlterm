<?php

namespace App\Http\Controllers;

use App\Temperature;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function report()
    {

        $datesGroup = Temperature::query()
            ->selectRaw('date')
            ->groupBy(DB::raw('date'))
            ->get()
            ->map(function($d) {
                return $d->date;
            });

        $records = Temperature::query()->select('id', 'date', 'air')
            ->whereNotNull('air')
            ->get();

        $data = $records->groupBy('id')
            ->map(function ($temperatures, $stationId) {
                return [
                    'name' => $stationId,
                    'data' => $temperatures->sortBy('date')->values()->map(function($t) {
                        return [
                            Carbon::createFromFormat('Y-m-d', $t->date)->timestamp*1000,
                            $t->air
                        ];
                    }),
                    'pointStart' => 1262304000000,
                    'pointInterval' => 24 * 3600 * 1000,
                ];
            })->values();

        return response()->json([
//            'dates' => $datesGroup,
            'data' => $data,
        ]);

    }
}
