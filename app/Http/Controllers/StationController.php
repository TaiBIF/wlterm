<?php

namespace App\Http\Controllers;

use App\Alage;
use App\BioMicroplastics;
use App\Main;
use App\Microplastics;
use App\Occurrence;
use App\RiverHabitat;
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
        $stationId = $request->get('id');
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


        $hasWaterQuery = WaterQuality::where('id', $stationId)->where(function ($query) {
            $query->where('project_id', 3)
                ->orWhere('project_id', 17)
                ->orWhere('project_id', 13)
                ->orWhere('project_id', 4)
                ->orWhere('project_id', 23);
        });
        $hasTemperatureQuery = Temperature::where('id', $stationId);

        $hasElementFluxQuery = WaterQuality::query()
            ->select(['water.id as water_id', 'date', 'locality_chinese', 'record_id'])
            ->where('project_id', 13)
            ->where('id', $stationId);

        $hasAlgaeDebrisQuery = Alage::where('id', $stationId);

        $hasOccurrenceQuery = Occurrence::where('sid', $stationId);

        $hasRiverHabitatQuery = RiverHabitat::where('river_section_number.station_id', $stationId)
            ->leftJoin('river_section_number', 'river_section_number.section', '=', 'river_habitat.section');

        $hasEnvMicroplasticQuery = Microplastics::where('sid', $stationId);

        $hasBioMicroplasticQuery = BioMicroplastics::where('sid', $stationId);

        if ($date) {
            $hasTemperatureQuery->where('date', 'like', '%' . $date . '%');
            $hasWaterQuery->where('date', 'like', '%' . $date . '%');
            $hasElementFluxQuery->where('date', 'like', '%' . $date . '%');
            $hasAlgaeDebrisQuery->where('date', 'like', '%' . $date . '%');
            $hasOccurrenceQuery->where('date', 'like', '%' . $date . '%');
            $hasRiverHabitatQuery->where('date', 'like', '%' . $date . '%');
            $hasEnvMicroplasticQuery->where('date', 'like', '%' . $date . '%');
            $hasBioMicroplasticQuery->where('date', 'like', '%' . $date . '%');
        }

        $hasOccurrence = $hasOccurrenceQuery->count();
        $hasTemperature = $hasTemperatureQuery->count();
        $hasWater = $hasWaterQuery->count();
        $hasElementFlux = $hasElementFluxQuery->count();
        $hasAlgaeDebris = $hasAlgaeDebrisQuery->count();
        $hasRiverHabitat = $hasRiverHabitatQuery->limit(1)->count();
        try {
            $hasEnvMicroplastic = $hasEnvMicroplasticQuery->limit(1)->count();
            $hasBioMicroplastic = $hasBioMicroplasticQuery->limit(1)->count();
        } catch (\Exception $e) {
            $hasEnvMicroplastic = 0;
            $hasBioMicroplastic = 0;
        }

        return response()
            ->json([
                'station' => $station,
                'occurrences' => $hasOccurrence,
                'water' => $hasWater,
                'temperature' => $hasTemperature,
                'elementFlux' => $hasElementFlux,
                'algaeDebris' => $hasAlgaeDebris,
                'riverHabitat' => $hasRiverHabitat,
                'envMicroplastic' => $hasEnvMicroplastic,
                'bioMicroplastic' => $hasBioMicroplastic,
                'coastalPlant' => 0,
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
                    ->where(function ($query) {
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
            } else if ($projectIdsString == '16,22') {
                $subquery = RiverHabitat::query()->select('station.id as id')
                    ->where(function ($query) {
                        $query->where('Project_id', 16)
                            ->orWhere('Project_id', 22);
                    })
                    ->leftJoin('river_section_number', 'river_section_number.section', '=', 'river_habitat.section')
                    ->leftJoin('station', 'river_section_number.station_id', '=', 'station.id');
            } else if ($projectIdsString == '30,31') {
                $subquery = Microplastics::query()->select('sid as id')
                    ->where(function ($query) {
                        $query->where('project_id', 30)
                            ->orWhere('project_id', 31);
                    });
            } else if ($projectIdsString == '32') {
                $subquery = BioMicroplastics::query()->select('sid as id')
                    ->where(function ($query) {
                        $query->where('project_id', 32);
                    });
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

            try {
                $markers = DB::table(DB::raw("({$subquery->toSql()}) as mystation"))
                    ->mergeBindings($subquery->getQuery())
                    ->join('station', 'station.id', '=', 'mystation.id')
                    ->select(['station.id', 'locality', 'locality_chinese', 'latitude', 'longitude'])
                    ->whereNotNull('station.latitude')
                    ->whereNotNull('station.longitude')
                    ->orderBy('station.latitude')
                    ->orderBy('station.longitude')
                    ->get();
            } catch (\Exception) {
                $markers = [];
            }

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
