<?php

namespace App\Http\Controllers;

use App\Alage;
use App\Main;
use App\WaterQuality;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RecordController extends Controller
{
    protected $perPage = 50;

    public function page(Request $request, $id)
    {
        $type = $request->get('type');

        if ($type === 'occurrence') {
            $record = Main::query()
                ->with('station:id,latitude,longitude,locality_chinese,maximum_elevation,maximum_depth,minimum_elevation,minimum_depth,locality_describe,coordinate_precision,全天光空域,直射光空域', 'project')
                ->join('species', 'species.scientific_name', '=', 'main.scientific_name')
                ->where('record_id', $id)
                ->first();
            $record->date = Carbon::createFromFormat('Y-m-d', $record->date)->format('Y-m-d');
        } else if ($type !== 'algae-debris') {
            $record = WaterQuality::query()
                ->with('station:id,latitude,longitude,locality_chinese,maximum_elevation,maximum_depth,minimum_elevation,minimum_depth,locality_describe,coordinate_precision,全天光空域,直射光空域', 'project')
                ->where('record_id', $id)
                ->first();
            $record->date = Carbon::createFromFormat('Y-m-d H:i:s', $record->date)->format('Y-m-d H:i:s');
        } else {
            $record = Alage::query()
                ->with('station:id,latitude,longitude,locality_chinese,maximum_elevation,maximum_depth,minimum_elevation,minimum_depth,locality_describe,coordinate_precision,全天光空域,直射光空域', 'project')
                ->where('record_id', $id)
                ->first();
            $record->date = Carbon::createFromFormat('Y-m-d H:i:s', $record->date)->format('Y-m-d');
        }

        return response()->json([
            'record' => $record,
        ]);

    }
}
