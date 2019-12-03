<?php

namespace App\Http\Controllers;

use App\Station;
use App\TableForGrid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StationController extends Controller
{
    protected $perPage = 50;

    public function index(Request $request)
    {
        $stationId = $request->get('auto_id');
        $localityName = $request->get('locality_chinese');
        $sort = $request->get('sort');
        $direction = $request->get('direction');

        $stationQuery = Station::query();

        if ($stationId) {
            $stationQuery->where('id', $stationId);
        }

        if ($localityName) {
            $stationQuery->where('locality_chinese', 'like', '%' . $localityName . '%');
        }

        if ($sort) {
            if ($sort === 'coordinate_precision') {
                $stationQuery->orderByRaw('`coordinate_precision`+0 ' . $direction);
            } else {
                $stationQuery->orderBy($sort, $direction);
            }
        }

        $stations = $stationQuery->paginate($this->perPage);

        return response()->json([
            'perPage' => $this->perPage,
            'total' => $stations->total(),
            'currentPage' => $stations->currentPage(),
            'data' => $stations->items(),
        ]);
    }

    public function map(Request $request)
    {

        if ($request->get('phylum')) {
            // SELECT *,locality_chinese as locality,sid as id FROM table_forgrid where `".$_GET['pfield']."` ".$qb.uniDecode($_GET['pvalue'], 'utf8'). $qe ." order by latitude,longitude
            $subquery = TableForGrid::query()
                ->select('sid')
                ->where('table_forgrid.phylum', $request->get('phylum'))
                ->groupBy('sid');

            $markers = DB::table(DB::raw("({$subquery->toSql()}) as mystation"))
                ->mergeBindings($subquery->getQuery())
                ->join('station', 'station.id', '=', 'mystation.sid')
                ->select(['locality', 'locality_chinese', 'latitude', 'longitude'])
                ->whereNotNull('station.latitude')
                ->whereNotNull('station.longitude')
                ->orderBy('station.latitude')
                ->orderBy('station.longitude')
                ->get();
        } else if ($request->get('class')) {
            $subquery = TableForGrid::query()
                ->select('sid')
                ->where('table_forgrid.class', $request->get('class'))
                ->groupBy('sid');

            $markers = DB::table(DB::raw("({$subquery->toSql()}) as mystation"))
                ->mergeBindings($subquery->getQuery())
                ->join('station', 'station.id', '=', 'mystation.sid')
                ->select(['locality', 'locality_chinese', 'latitude', 'longitude'])
                ->whereNotNull('station.latitude')
                ->whereNotNull('station.longitude')
                ->orderBy('station.latitude')
                ->orderBy('station.longitude')
                ->get();
        } else if ($request->get('order')) {
            $subquery = TableForGrid::query()
                ->select('sid')
                ->where('table_forgrid.order', $request->get('order'))
                ->groupBy('sid');

            $markers = DB::table(DB::raw("({$subquery->toSql()}) as mystation"))
                ->mergeBindings($subquery->getQuery())
                ->join('station', 'station.id', '=', 'mystation.sid')
                ->select(['locality', 'locality_chinese', 'latitude', 'longitude'])
                ->whereNotNull('station.latitude')
                ->whereNotNull('station.longitude')
                ->orderBy('station.latitude')
                ->orderBy('station.longitude')
                ->get();
        } else {
            // SELECT *  FROM station   where (not latitude is null) and id < 14 order by latitude,longitude
            $markers = Station::query()
                ->select(['locality', 'locality_chinese', 'latitude', 'longitude'])
                ->whereNotNull('latitude')
                ->where('id', '<', 14)
                ->orderBy('latitude')
                ->orderBy('longitude')
                ->get();
        }

        return response()
            ->json($markers);
    }
}
