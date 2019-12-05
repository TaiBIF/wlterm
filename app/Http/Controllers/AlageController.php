<?php

namespace App\Http\Controllers;

use App\Alage;
use Illuminate\Http\Request;

class AlageController extends Controller
{
    protected $perPage = 50;

    public function index(Request $request)
    {
        $locality = $request->get('locality_chinese');
        $date = $request->get('date');
        $id = $request->get('station_id');
        $sort = $request->get('sort');
        $direction = $request->get('direction', 'asc');

        $recordsQuery = Alage::query()
            ->select([
                'alage.id as alage_id',
                'record_id',
                'date',
                'station.id as station_id',
                'station.latitude',
                'station.longitude',
                'station.locality_chinese',
                'station.maximum_elevation',
                'station.maximum_depth'
            ])
            ->join('station', 'alage.id', '=', 'station.id');

        if ($locality) {
            $recordsQuery->where('station.locality_chinese', 'like', '%' . $locality . '%');
        }

        if ($id) {
            $recordsQuery->where('station.id', $id);
        }

        if ($date) {
            $recordsQuery->where('date',  'like', '%' . $date . '%');
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
