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
        $collectorChinese = $request->get('collector_chinese');
        $itemChinese = $request->get('item_chinese');

        $recordsQuery = Alage::query()
            ->select([
                'alage.id as alage_id',
                'collector_chinese',
                'item_chinese',
                'biomass',
                'unit',
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

        if ($collectorChinese) {
            $recordsQuery->where('collector_chinese', 'like', '%' . $collectorChinese . '%');
        }

        if ($itemChinese) {
            $recordsQuery->where('item_chinese', 'like', '%' . $itemChinese . '%');
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
