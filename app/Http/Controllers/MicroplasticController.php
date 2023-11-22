<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Microplastics;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MicroplasticController extends Controller
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

        $recordsQuery = Microplastics::query()
            ->select([
                'station.id as station_id',
                'station.locality_chinese',
                'date',
                'collector_chinese',
                'item_chinese',
                'record_id',
                'pet',
                'pe',
                'pvc',
                'pp',
                'ps',
                'pc',
                'pa',
                'pe_pp',
                'rayon',
                'eea',
                'mps',
                'unit',
            ])
            ->leftJoin('station', 'microplastic.sid', '=', 'station.id');

        if ($locality) {
            $recordsQuery->where('station.locality_chinese', 'like', '%' . $locality . '%');
        }

        if ($id) {
            $recordsQuery->where('station.id', $id);
        }

        if ($date) {
            $recordsQuery->where('date', 'like', '%' . $date . '%');
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

    public function get(Request $request, $id)
    {
        $record = Microplastics::query()
            ->with(
                'station:id,latitude,longitude,locality_chinese,maximum_elevation,maximum_depth,minimum_elevation,minimum_depth,locality_describe,coordinate_precision,全天光空域,直射光空域',
                'project',
            )
            ->where('record_id', $id)
            ->first();

        $record->date = Carbon::createFromFormat('Y-m-d', $record->date)->format('Y-m-d');

        return response()->json([
            'record' => $record,
        ]);
    }
}
