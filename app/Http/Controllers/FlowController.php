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
            ->select(['flow.*', 'flow_stations.name as station_name', 'rain.st1 as rain_st1', 'rain.st2 as rain_st2', 'rain.st3 as rain_st3', 'rain.st4 as rain_st4'])
            ->join('flow_stations', 'flow.station_id', '=', 'flow_stations.id')
            ->join('rain', 'rain.date', '=', 'flow.date');

        if ($date) {
            $flowRecordsQuery->where('flow.date',  'like', '%' . $date . '%');
        }

        if ($name) {
            $flowRecordsQuery->where('flow_stations.name', 'like', '%' . $name . '%');
        }

        if ($sort && $direction) {
            $sort = $sort !== 'station_name' ? $sort : 'flow_stations.name';
            $flowRecordsQuery->orderBy($sort, $direction);
        }

        $flowRecords = $flowRecordsQuery->paginate($this->perPage);

        return response()->json([
            'total' => $flowRecords->total(),
            'currentPage' => $flowRecords->currentPage(),
            'perPage' => $flowRecords->perPage(),
            'data' => $flowRecords->items(),
        ]);
    }

    public function report(Request $request)
    {
        $name = $request->get('station_name');

        $recordsQuery = Flow::select('station_id', 'date', 'public', 'simu')
            ->whereNotNull('public');

        if ($name) {
            $recordsQuery->join('flow_stations', 'flow.station_id', '=', 'flow_stations.id')
                ->where('flow_stations.name', 'like', '%' . $name . '%');
        }

        $records = $recordsQuery->get();

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
