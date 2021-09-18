<?php

namespace App\Http\Controllers;

use App\Main;
use App\Project;
use App\Station;
use Illuminate\Http\Request;

class SimplingEventController extends Controller
{
    private $perPage = 50;

    public function index(Request $request)
    {
        $date = $request->get('date');
        $id = $request->get('id');
        $examineWay = $request->get('examine_way');
        $localityChinese = $request->get('locality_chinese');
        $projectName = $request->get('project_name');

        $eventsQuery = Main::query()
            ->select([
                'date', 'examine_way', 'main.id as id', 'main.Project_id as project_id',
                'station.locality_chinese as locality_chinese',
                'station.id as station_id',
                'station.latitude as latitude',
                'station.longitude as longitude',
                'project.projectname as project_name'
            ])
            ->leftJoin('station', 'station.id', '=', 'main.id')
            ->leftJoin('project', 'project.Project_id', '=', 'main.Project_id')
            ->orderBy('date')
            ->orderBy('main.Project_id')
            ->groupBy(['date', 'examine_way', 'main.id', 'main.Project_id']);

        if ($date) {
            $eventsQuery->where('date', 'like', '%' . $date . '%');
        }

        if ($examineWay) {
            $eventsQuery->where('examine_way', 'like', '%' . $examineWay . '%');
        }

        if ($id) {
            $eventsQuery->where('station.id', $id);
        }

        if ($localityChinese) {
            $eventsQuery->where('station.locality_chinese', 'like', '%' . $localityChinese . '%');
        }

        if ($projectName) {
            $eventsQuery->where('project.projectname', 'like', '%' . $projectName . '%');
        }

        $events = $eventsQuery->paginate($this->perPage);

        return response()->json([
            'total' => $events->total(),
            'currentPage' => $events->currentPage(),
            'data' => $events->items(),
        ]);
    }

    public function events($projectId, $stationId, Request $request)
    {
        $examineWay = $request->get('examineWay');
        $date = $request->get('date');

        $eventsQuery = Main::query()
            ->select([
                'species.order', 'species.family', 'main.scientific_name', 'chinese',
                'individual_count', 'cover_rate', 'body_length', 'body_length_unit',
                'biomass', 'biomass_unit', 'notes', 'collector_chinese', 'identified_by_chinese', 'record_use_name'
            ])
            ->leftJoin('species', 'species.scientific_name', 'main.scientific_name');

        if ($examineWay) {
            $eventsQuery->where('examine_way', $examineWay);
        }

        if ($date) {
            $eventsQuery->where('date', $date);
        }

        $events = $eventsQuery->paginate($this->perPage);

        return response()->json([
            'station' => Station::find($stationId),
            'project' => Project::where('Project_id', $projectId)->first(),
            'total' => $events->total(),
            'currentPage' => $events->currentPage(),
            'data' => $events->items(),
        ]);

    }
}
