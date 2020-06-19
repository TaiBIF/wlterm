<?php

namespace App\Http\Controllers;

use App\Flow;
use App\FlowStation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FlowController extends Controller
{
    protected $perPage = 50;

    public function index(Request $request)
    {
        $name = $request->get('station_name');
        $date = $request->get('date');

        $sort = $request->get('sort');
        $direction = $request->get('direction', 'asc');

        $flowRecordsQuery = Flow::query()
            ->join('flow_stations', 'flow.station_id', '=', 'flow_stations.id')
            ->with(['rain', 'station']);

        if ($date) {
            $flowRecordsQuery->where('date',  'like', '%' . $date . '%');
        }

        if ($name) {
            $flowRecordsQuery->where('flow_stations.name', 'like', '%' . $name . '%');
        }

        if ($sort && $direction) {
            $sort = $sort !== 'station_name' ? $sort : 'flow_stations.name';
            $flowRecordsQuery->orderBy($sort, $direction);
        }

        $flowRecords = $flowRecordsQuery->paginate($this->perPage);

        $data = $flowRecords->map(function ($record) {
            $record->station_name =$record->station->name ?? '';
            $record->rain_st1 = $record->rain->st1;
            $record->rain_st2 = $record->rain->st2;
            $record->rain_st3 = $record->rain->st3;
            $record->rain_st4 = $record->rain->st4;
            unset($record->rain, $record->station);
            return $record;
        });

        return response()->json([
            'total' => $flowRecords->total(),
            'currentPage' => $flowRecords->currentPage(),
            'perPage' => $flowRecords->perPage(),
            'data' => $data,
        ]);
    }

    public function report()
    {
        $records = Flow::select('station_id', 'date', 'public', 'simu')->get();
        $stations = FlowStation::all()->keyBy('id');

        $data = $records->groupBy('station_id')
            ->map(function ($records, $stationId) use ($stations) {
                return [
                    'name' => $stations[$stationId]->name,
                    'data' => $records->sortBy('date')->values()->map(function($t) {
                        return [
                            Carbon::createFromFormat('Y-m-d', $t->date)->timestamp*1000,
                            $t->public
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
