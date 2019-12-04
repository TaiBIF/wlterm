<?php

namespace App\Http\Controllers;

use App\Flow;
use Illuminate\Http\Request;

class FlowController extends Controller
{
    protected $perPage = 50;

    public function index(Request $request)
    {
        $id = $request->get('station_id');
        $date = $request->get('date');

        $flowRecordsQuery = Flow::query()
            ->with(['rain', 'station']);

        if ($date) {
            $flowRecordsQuery->where('date',  'like', '%' . $date . '%');
        }

        if ($id) {
            $flowRecordsQuery->where('station_id',  $id);
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
}
