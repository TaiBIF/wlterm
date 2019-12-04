<?php

namespace App\Http\Controllers;

use App\Main;
use App\Occurrence;
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

        $occurrencesQuery = Occurrence::query();
        if ($phylum) {
            $occurrencesQuery->where('phylum', $phylum);
        }

        if ($date) {
            $occurrencesQuery->where('date', 'like', '%' . $date . '%');
        }

        if ($class) {
            $occurrencesQuery->where('class', 'like', '%' . $class . '%');
        }

        if ($order) {
            $occurrencesQuery->where('order', 'like', '%' . $order . '%');
        }

        if ($family) {
            $occurrencesQuery->where('family', 'like', '%' . $family . '%');
        }

        if ($collectorChinese) {
            $occurrencesQuery->where('collector_chinese', 'like', '%' . $collectorChinese . '%');
        }

        if ($examWay) {
            $occurrencesQuery->where('examine_way', 'like', '%' . $examWay . '%');
        }

        if ($scientificName) {
            $occurrencesQuery->where('scientific_name', 'like', '%' . $scientificName . '%');
        }

        if ($locality) {
            $occurrencesQuery->where('locality_chinese', 'like', '%' . $locality . '%');
        }

        if ($latitude) {
            $occurrencesQuery->where('latitude', 'like', '%' . $latitude . '%');
        }

        if ($longitude) {
            $occurrencesQuery->where('longitude', 'like', '%' . $locality . '%');
        }


        if ($chineseName) {
            $occurrencesQuery->where('chinese', 'like', '%' . $chineseName . '%');
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

    public function report()
    {
        $occurrenceYearsCount = Occurrence::selectRaw('YEAR(date) as year, count(YEAR(date)) as count')
            ->groupBy(DB::raw('YEAR(date)'))
            ->get();

        return response()->json([
            'years' => $occurrenceYearsCount->map(function ($data) {
                return $data->year;
            }),
            'data' => $occurrenceYearsCount
        ]);
    }
}
