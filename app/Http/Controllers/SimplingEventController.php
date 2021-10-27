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

        $sort = $request->get('sort');
        $direction = $request->get('direction', 'asc');

        $eventsQuery = Main::query()
            ->select([
                'date', 'examine_way', 'main.id as id', 'main.Project_id as project_id',
                'station.locality_chinese as locality_chinese',
                'station.id as station_id',
                'station.latitude as latitude',
                'station.longitude as longitude',
                'project.projectname as project_name'
            ])
            ->join('station', 'station.id', '=', 'main.id')
            ->join('project', 'project.Project_id', '=', 'main.Project_id')
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

        if ($sort || $direction) {
            $eventsQuery->orderBy($sort, $direction);
        } else {
            $eventsQuery->orderBy('date')
                ->orderBy('main.Project_id');
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
        $order = $request->get('order');
        $family = $request->get('family');
        $recordUseName = $request->get('record_use_name');
        $scientificName = $request->get('scientific_name');
        $chinese = $request->get('chinese');
        $bodyLength = $request->get('body_length');
        $bodyLengthUnit = $request->get('body_length_unit');
        $biomass = $request->get('biomass');
        $biomassUnit = $request->get('biomass_unit');
        $notes = $request->get('notes');

        $sort = $request->get('sort');
        $direction = $request->get('direction', 'asc');

        $eventsQuery = Main::query()
            ->select([
                'species.order', 'species.family', 'main.scientific_name', 'chinese',
                'individual_count', 'cover_rate', 'body_length', 'body_length_unit',
                'biomass', 'biomass_unit', 'notes', 'collector_chinese', 'identified_by_chinese', 'record_use_name'
            ])
            ->leftJoin('species', 'species.scientific_name', 'main.scientific_name');

        if ($examineWay) {
            $eventsQuery->where('examine_way', '=', $examineWay);
        }

        if ($date) {
            $eventsQuery->where('date', '=', $date);
        }

        if ($order) {
            $eventsQuery->where('species.order', 'like', "%$order%");
        }

        if ($family) {
            $eventsQuery->where('species.family', 'like', "%$family%");
        }

        if ($recordUseName) {
            $eventsQuery->where('record_use_name', 'like', "%$recordUseName%");
        }

        if ($scientificName) {
            $eventsQuery->where('main.scientific_name', 'like', "%$scientificName%");
        }

        if ($chinese) {
            $eventsQuery->where('chinese', 'like', "%$chinese%");
        }

        if ($bodyLength) {
            $eventsQuery->where('body_length', 'like', "%$bodyLength%");
        }

        if ($bodyLengthUnit) {
            $eventsQuery->where('body_length_unit', 'like', "%$bodyLengthUnit%");
        }

        if ($biomass) {
            $eventsQuery->where('biomass', '=', $biomass);
        }

        if ($biomassUnit) {
            $eventsQuery->where('biomass_unit', 'like', "%$biomassUnit%");
        }

        if ($notes) {
            $eventsQuery->where('notes', 'like', "%$notes%");
        }

        if ($sort || $direction) {
            $eventsQuery->orderBy($sort, $direction);
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
