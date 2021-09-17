<?php

namespace App\Http\Controllers;

use App\Alage;
use App\Main;
use App\Occurrence;
use App\Station;
use App\TableForGrid;
use App\Temperature;
use App\WaterQuality;
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

    public function get($stationId, Request $request)
    {
        $date = $request->get('date');

        $station = Station::query()
            ->select(['id', 'locality', 'locality_chinese', 'latitude', 'longitude'])
            ->where('id', $stationId)
            ->orderBy('latitude')
            ->orderBy('longitude')
            ->first();


        $hasWaterQuery = WaterQuality::where('id', $stationId)->where(function($query) {
            $query->where('project_id', 3)
                ->orWhere('project_id', 17)
                ->orWhere('project_id', 13)
                ->orWhere('project_id', 4)
                ->orWhere('project_id', 23);
        });
        $hasTemperatureQuery = Temperature::where('id', $stationId);

        $hasElementFluxQuery =  WaterQuality::query()
            ->select(['water.id as water_id', 'date', 'locality_chinese', 'record_id'])
            ->where('project_id', 13)
            ->where('id', $stationId);

        $hasAlgaeDebrisQuery = Alage::where('id', $stationId);

        $hasOccurrenceQuery = Occurrence::where('sid', $stationId);

        if ($date) {
            $hasTemperatureQuery->where('date', 'like', '%' . $date . '%');
            $hasWaterQuery->where('date', 'like', '%' . $date . '%');
            $hasElementFluxQuery->where('date', 'like', '%' . $date . '%');
            $hasAlgaeDebrisQuery->where('date', 'like', '%' . $date . '%');
            $hasOccurrenceQuery->where('date', 'like', '%' . $date . '%');
        }

        $hasOccurrence = $hasOccurrenceQuery->count();
        $hasTemperature = $hasTemperatureQuery->count();
        $hasWater = $hasWaterQuery->count();
        $hasElementFlux = $hasElementFluxQuery->count();
        $hasAlgaeDebris = $hasAlgaeDebrisQuery->count();
        return response()
            ->json([
                'station' => $station,
                'occurrences' => $hasOccurrence,
                'water' => $hasWater,
                'temperature' => $hasTemperature,
                'elementFlux' => $hasElementFlux,
                'algaeDebris' => $hasAlgaeDebris,
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
                ->select(['id', 'locality', 'locality_chinese', 'latitude', 'longitude'])
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
                ->select(['id', 'locality', 'locality_chinese', 'latitude', 'longitude'])
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
                ->select(['id', 'locality', 'locality_chinese', 'latitude', 'longitude'])
                ->whereNotNull('station.latitude')
                ->whereNotNull('station.longitude')
                ->orderBy('station.latitude')
                ->orderBy('station.longitude')
                ->get();
        } else if ($request->get('family')) {
            $subquery = TableForGrid::query()
                ->select('sid')
                ->where('table_forgrid.family', $request->get('family'))
                ->groupBy('sid');

            $markers = DB::table(DB::raw("({$subquery->toSql()}) as mystation"))
                ->mergeBindings($subquery->getQuery())
                ->join('station', 'station.id', '=', 'mystation.sid')
                ->select(['id', 'locality', 'locality_chinese', 'latitude', 'longitude'])
                ->whereNotNull('station.latitude')
                ->whereNotNull('station.longitude')
                ->orderBy('station.latitude')
                ->orderBy('station.longitude')
                ->get();
        } else if ($request->get('scientific_name')) {
            $subquery = TableForGrid::query()
                ->select('sid')
                ->where('table_forgrid.scientific_name', $request->get('scientific_name'))
                ->groupBy('sid');

            $markers = DB::table(DB::raw("({$subquery->toSql()}) as mystation"))
                ->mergeBindings($subquery->getQuery())
                ->join('station', 'station.id', '=', 'mystation.sid')
                ->select(['id', 'locality', 'locality_chinese', 'latitude', 'longitude'])
                ->whereNotNull('station.latitude')
                ->whereNotNull('station.longitude')
                ->orderBy('station.latitude')
                ->orderBy('station.longitude')
                ->get();
        } else if ($request->get('project_ids') || $request->get('date')) {
            $projectIdsString = $request->get('project_ids');
            $projectIds = $projectIdsString ? explode(',', $projectIdsString) : '';
            $date = $request->get('date');


            if ($projectIdsString === '3,4,13,17,23') {
                $subquery = WaterQuality::query()->select('id')
                    ->where(function($query) {
                        $query->where('project_id', 3)
                            ->orWhere('project_id', 4)
                            ->orWhere('project_id', 13)
                            ->orWhere('project_id', 17)
                            ->orWhere('project_id', 23);
                    });

                if (!empty($projectIds)) {
                    $subquery->whereIn('Project_id', $projectIds);
                }
            } else if ($projectIdsString == '13') {
                $subquery = WaterQuality::query()->select('id')
                    ->where('project_id', 13);

                if (!empty($projectIds)) {
                    $subquery->whereIn('Project_id', $projectIds);
                }
            } else if ($projectIdsString == '14') {
                $subquery = Temperature::query()->select('id');
            } else if ($projectIdsString == '1,15') {
                $subquery = Alage::query()->select('id');
            } else {
                $subquery = Main::query()->select('main.id');

                if (!empty($projectIds)) {
                    $subquery->whereIn('Project_id', $projectIds);
                }
            }

            if ($date) {
                $subquery->where('date', 'like', '%' . $date . '%');
            }

            $subquery = $subquery->groupBy('id');

            $markers = DB::table(DB::raw("({$subquery->toSql()}) as mystation"))
                ->mergeBindings($subquery->getQuery())
                ->join('station', 'station.id', '=', 'mystation.id')
                ->select(['station.id', 'locality', 'locality_chinese', 'latitude', 'longitude'])
                ->whereNotNull('station.latitude')
                ->whereNotNull('station.longitude')
                ->orderBy('station.latitude')
                ->orderBy('station.longitude')
                ->get();
        } else {
            // SELECT *  FROM station   where (not latitude is null) and id < 14 order by latitude,longitude
            $markersQuery = Station::query();
            if ($request->get('id')) {
                $markersQuery->where('id', $request->get('id'));
            }

            $markers = $markersQuery->select(['id', 'locality', 'locality_chinese', 'latitude', 'longitude'])
                ->whereNotNull('latitude')
                ->orderBy('latitude')
                ->orderBy('longitude')
                ->get();

        }

        return response()
            ->json($markers);
    }
}
