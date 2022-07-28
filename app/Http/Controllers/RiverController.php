<?php

namespace App\Http\Controllers;

use App\RiverBsTp;
use App\RiverHabitat;
use App\RiverMorphology;
use App\RiverSubstrate;
use App\RiverSection;
use App\Station;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiverController extends Controller
{
    private $perPage = 50;

    public function habitat(Request $request)
    {
        $date = $request->get('date');
        $section = $request->get('section');
        $river = $request->get('river');
        $localityChinese = $request->get('locality_chinese');
        $stationId = $request->get('station_id');

        $sort = $request->get('sort');
        $direction = $request->get('direction', 'asc');

        $morphology = RiverMorphology::all()->keyBy('morphology_id')->toArray();
        $substrate = RiverSubstrate::all()->keyBy('substrate_id')->toArray();
        $habitateQuery = RiverHabitat::query()
            ->select('river_habitat.*', 'station.locality_chinese', 'station.id as station_id')
            ->leftJoin('river_section_number', 'river_section_number.section', '=', 'river_habitat.section')
            ->leftJoin('station', 'river_section_number.station_id', '=', 'station.id');

        if ($date) {
            $habitateQuery->where('date', 'like', '%' . $date . '%');
        }

        if ($section) {
            $habitateQuery->where('section', $section);
        }

        if ($river) {
            $habitateQuery->where('river_habitat.river', 'like', '%' . $river . '%');
        }

        if ($localityChinese) {
            $habitateQuery->where('station.locality_chinese', 'like', '%' . $localityChinese . '%');
        }

        if ($stationId) {
            $habitateQuery->where('station.id', $stationId);
        }

        if ($sort && $direction) {
            if ($sort === 'morphology_name') $sort = 'morphology';
            $habitateQuery->orderBy($sort, $direction);
        }

        $habitateRecords = $habitateQuery->paginate($this->perPage);

        $records = $habitateRecords->map(function ($item) use ($morphology, $substrate) {
            $item->morphology_name = $morphology[$item->morphology]['morphology_chinese'];
            $item->substrate_name = $substrate[$item->substrate]['substrate_chinese'] ?? '';
            return $item;
        });

        return response()->json([
            'total' => $habitateRecords->total(),
            'currentPage' => $habitateRecords->currentPage(),
            'data' => $records,
        ]);
    }

    public function habitatReport(Request $request)
    {
        $date = $request->get('date');
        $section = $request->get('section');
        $river = $request->get('river');
        $localityChinese = $request->get('locality_chinese');
        $stationId = $request->get('station_id');

        $morphology = RiverMorphology::all()->keyBy('morphology_id')->toArray();
        $substrate = RiverSubstrate::all()->keyBy('substrate_id')->toArray();
        $habitateQuery = RiverHabitat::query()
            ->select(
                'river_habitat.river',
                'river_habitat.morphology',
                'river_habitat.substrate',
                'river_habitat.date',
                'river_section_number.station_id',
            )
            ->leftJoin('river_section_number', 'river_section_number.section', '=', 'river_habitat.section')
            ->leftJoin('station', 'river_section_number.station_id', '=', 'station.id')
            ->whereNotNull('station.id');

        if ($date) {
            $habitateQuery->where('date', 'like', '%' . $date . '%');
        }

        if ($section) {
            $habitateQuery->where('section', $section);
        }

        if ($river) {
            $habitateQuery->where('river_habitat.river', 'like', '%' . $river . '%');
        }

        if ($localityChinese) {
            $habitateQuery->where('station.locality_chinese', 'like', '%' . $localityChinese . '%');
        }

        if ($stationId) {
            $habitateQuery->where('station.id', '=', $stationId);
        }

        $stations = $habitateQuery->get()->unique('station_id')->sortBy('station_id')->pluck('station_id');

        $activeStation = $request->get('active_station') ?? $stations->first();

        if ($activeStation) {
            $habitateQuery->where('station.id', $activeStation);
        }

        $habitateRecords = $habitateQuery->get();

        $dates = $habitateRecords->unique('date')->pluck('date');

        return response()->json([
            'data' => [
                'stations' => Station::whereIn('id', $stations)->get(),
                'categories' => $dates->map(function($date) {
                    return Carbon::createFromFormat('Y-m-d', $date)->format('Y-m');
                })->all(),
                'morphology' => $habitateRecords->sortByDesc('morphology')->groupBy('morphology')
                    ->map(function ($record, $key) use ($morphology, $dates) {
                        $dateRecords = $record->groupBy('date')
                            ->map(function ($r) {
                                return $r->count();
                            });
                        return [
                            'name' => $morphology[$key]['morphology_chinese'],
                            'data' => $dates
                                ->map(function ($date) use ($dateRecords) {
                                    return $dateRecords[$date] ?? 0;
                                }),
                        ];
                    })->values(),
                'substrate' => $habitateRecords->sortByDesc('substrate')->groupBy('substrate')
                    ->map(function ($record, $key) use ($substrate, $dates) {
                        $dateRecords = $record
                            ->groupBy('date')
                            ->map(function ($r) {
                                return $r->count();
                            });
                        return [
                            'name' => $substrate[$key]['substrate_chinese'] ?? '#',
                            'data' => $dates
                                ->map(function ($date) use ($dateRecords) {
                                    return $dateRecords[$date] ?? 0;
                                }),
                        ];
                    })->values(),
            ],
        ]);
    }

    public function section(Request $request)
    {
        $date = $request->get('date');
        $section = $request->get('section');
        $river = $request->get('river');

        $sort = $request->get('sort');
        $direction = $request->get('direction', 'asc');

        $sectionQuery = RiverSection::query();

        if ($date) {
            $sectionQuery->where('date', 'like', '%' . $date . '%');
        }

        if ($section) {
            $sectionQuery->where('section', $section);
        }

        if ($river) {
            $sectionQuery->where('river', 'like', '%' . $river . '%');
        }

        if ($sort && $direction) {
            $sectionQuery->orderBy($sort, $direction);
        }

        $habitateRecords = $sectionQuery->paginate($this->perPage);
        $records = $habitateRecords->map(function ($item) {
            return $item;
        });

        return response()->json([
            'total' => $habitateRecords->total(),
            'currentPage' => $habitateRecords->currentPage(),
            'data' => $habitateRecords->items(),
        ]);
    }

    public function certainSection($id, $date)
    {
        $bsTp = RiverBsTp::where('bs_and_tp', $id)->where('date', $date)->first();
        $sections = RiverSection::where('bs_and_tp', $id)
            ->where('date', $date)
            ->orderBy('section')
            ->orderBy('measure_point')
            ->get();

        return response()->json([
            'bs_tp' => $bsTp,
            'sections' => $sections->groupBy('section'),
        ]);
    }
}
