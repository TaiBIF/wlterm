<?php

namespace App\Http\Controllers;

use App\Main;
use App\Occurrence;
use App\Station;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OccurrenceController extends Controller
{
    protected $perPage = 50;

    public function index(Request $request)
    {
        $family = $request->get('family');
        $class = $request->get('class');
        $phylum = $request->get('phylum');
        $order = $request->get('order');
        $scientificName = $request->get('scientific_name');
        $chineseName = $request->get('chinese');
        $date = $request->get('date');
        $locality = $request->get('locality_chinese');
        $collectorChinese = $request->get('collector_chinese');
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $examWay = $request->get('examine_way');
        $sort = $request->get('sort');
        $direction = $request->get('direction', 'asc');
        $identifiedByChinese = $request->get('identified_by_chinese');
        $sid = $request->get('sid');
        $projectIds = $request->get('projectIds');
        $recordUseName = $request->get('record_use_name');

        $occurrencesQuery = Occurrence::query()
            ->select('table_forgrid.*', 'main.record_use_name')
            ->leftJoin('main', 'main.record_id', '=', 'table_forgrid.record_id');

        if ($sid) {
            $occurrencesQuery->where('sid', '=', $sid);
        }

        if ($phylum) {
            $occurrencesQuery->where('phylum', 'like', '%' . $phylum . '%');
        }

        if ($date) {
            $occurrencesQuery->where('table_forgrid.date', 'like', '%' . $date . '%');
        }

        if ($class) {
            $occurrencesQuery->where('class', 'like', '%' . $class . '%');
        }

        if ($order) {
            $occurrencesQuery->where('order', 'like', '%' . $order . '%');
        }

        if ($family) {
            $occurrencesQuery->where('table_forgrid.family', 'like', '%' . $family . '%');
        }

        if ($collectorChinese) {
            $occurrencesQuery->where('collector_chinese', 'like', '%' . $collectorChinese . '%');
        }

        if ($examWay) {
            $occurrencesQuery->where('examine_way', 'like', '%' . $examWay . '%');
        }

        if ($scientificName) {
            $occurrencesQuery->where('table_forgrid.scientific_name', 'like', '%' . $scientificName . '%');
        }

        if ($locality) {
            $occurrencesQuery->where('locality_chinese', 'like', '%' . $locality . '%');
        }

        if ($latitude) {
            $occurrencesQuery->where('latitude', 'like', '%' . $latitude . '%');
        }

        if ($longitude) {
            $occurrencesQuery->where('longitude', 'like', '%' . $longitude . '%');
        }

        if ($chineseName) {
            $occurrencesQuery->where('chinese', 'like', '%' . $chineseName . '%');
        }

        if ($recordUseName) {
            $occurrencesQuery->where('record_use_name', 'like', '%' . $recordUseName . '%');
        }

        if ($identifiedByChinese) {
            $occurrencesQuery->where('table_forgrid.identified_by_chinese', 'like', '%' . $identifiedByChinese . '%');
        }

        if ($projectIds) {
            $projectIdsArray = explode(',', $projectIds);
            $occurrencesQuery->leftJoin('main', 'main.record_id', 'table_forgrid.record_id')
                ->whereIn('main.Project_id', $projectIdsArray);
        }

        if ($sort && $direction) {
            $occurrencesQuery->orderBy($sort, $direction);
        } else {
            $occurrencesQuery->orderBy('sid', 'asc');
        }

        $occurrencesPage = $occurrencesQuery->paginate($this->perPage);
        return response()->json([
            'total' => $occurrencesPage->total(),
            'currentPage' => $occurrencesPage->currentPage(),
            'data' => $occurrencesPage->items(),
            'perPage' => $occurrencesPage->perPage(),
        ]);
    }

    public function listmap(Request $request)
    {
        $family = $request->get('family');
        $class = $request->get('class');
        $phylum = $request->get('phylum');
        $order = $request->get('order');
        $scientificName = $request->get('scientific_name');
        $chineseName = $request->get('chinese');
        $date = $request->get('date');
        $locality = $request->get('locality_chinese');
        $collectorChinese = $request->get('collector_chinese');
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $examWay = $request->get('examine_way');
        $identifiedByChinese = $request->get('identified_by_chinese');
        $sid = $request->get('sid');
        $projectIds = $request->get('projectIds');
        $recordUseName = $request->get('record_use_name');

        $occurrencesQuery = Occurrence::query()
            ->select('table_forgrid.*', 'main.record_use_name')
            ->leftJoin('main', 'main.record_id', '=', 'table_forgrid.record_id');

        if ($sid) {
            $occurrencesQuery->where('sid', '=', $sid);
        }

        if ($phylum) {
            $occurrencesQuery->where('phylum', 'like', '%' . $phylum . '%');
        }

        if ($date) {
            $occurrencesQuery->where('table_forgrid.date', 'like', '%' . $date . '%');
        }

        if ($class) {
            $occurrencesQuery->where('class', 'like', '%' . $class . '%');
        }

        if ($order) {
            $occurrencesQuery->where('order', 'like', '%' . $order . '%');
        }

        if ($family) {
            $occurrencesQuery->where('table_forgrid.family', 'like', '%' . $family . '%');
        }

        if ($collectorChinese) {
            $occurrencesQuery->where('collector_chinese', 'like', '%' . $collectorChinese . '%');
        }

        if ($examWay) {
            $occurrencesQuery->where('examine_way', 'like', '%' . $examWay . '%');
        }

        if ($scientificName) {
            $occurrencesQuery->where('table_forgrid.scientific_name', 'like', '%' . $scientificName . '%');
        }

        if ($locality) {
            $occurrencesQuery->where('locality_chinese', 'like', '%' . $locality . '%');
        }

        if ($latitude) {
            $occurrencesQuery->where('latitude', 'like', '%' . $latitude . '%');
        }

        if ($longitude) {
            $occurrencesQuery->where('longitude', 'like', '%' . $longitude . '%');
        }

        if ($chineseName) {
            $occurrencesQuery->where('chinese', 'like', '%' . $chineseName . '%');
        }

        if ($recordUseName) {
            $occurrencesQuery->where('record_use_name', 'like', '%' . $recordUseName . '%');
        }

        if ($identifiedByChinese) {
            $occurrencesQuery->where('table_forgrid.identified_by_chinese', 'like', '%' . $identifiedByChinese . '%');
        }

        if ($projectIds) {
            $projectIdsArray = explode(',', $projectIds);
            $occurrencesQuery->leftJoin('main', 'main.record_id', 'table_forgrid.record_id')
                ->whereIn('main.Project_id', $projectIdsArray);
        }

        $stationIds = $occurrencesQuery->select('sid')->groupBy('sid')->get()->unique('sid')->pluck('sid');

        $stations = Station::select(['id', 'locality', 'locality_chinese', 'latitude', 'longitude'])
            ->whereNotNull('station.latitude')
            ->whereNotNull('station.longitude')
            ->whereIn('id', $stationIds)
            ->orderBy('station.latitude')
            ->orderBy('station.longitude')
            ->get();

        return response()->json([
            'data' => $stations,
        ]);
    }

    public function show($id)
    {
        $record = Main::query()
            ->with('station', 'project', 'species:scientific_name,author,chinese,family,family_c')
            ->where('record_id', $id)
            ->first();

        return response()->json([
            'occurence' => $record,
        ]);
    }

    public function years()
    {
        $occurrenceYears = Occurrence::selectRaw('YEAR(date) as year')
            ->groupBy(DB::raw('YEAR(date)'))
            ->orderBy('year')
            ->get();

        return response()->json([
            'years' => $occurrenceYears->pluck('year'),
        ]);
    }

    public function report()
    {
        $occurrenceYearsCount = Occurrence::selectRaw('YEAR(date) as year, count(YEAR(date)) as count')
            ->groupBy(DB::raw('YEAR(date)'))
            ->orderBy('year')
            ->get();

        return response()->json([
            'years' => $occurrenceYearsCount->map(function ($data) {
                return $data->year;
            }),
            'data' => $occurrenceYearsCount
        ]);
    }
}
