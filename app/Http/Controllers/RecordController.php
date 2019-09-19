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

        if ($type !== 'algae-debris') {
            $record = WaterQuality::query()
                ->with('station:id,latitude,longitude,locality_chinese,maximum_elevation,maximum_depth', 'project')
                ->where('record_id', $id)
                ->first();
            $record->date = Carbon::createFromFormat('Y-m-d H:i:s', $record->date)->format('Y-m-d');
        } else {
            $record = Alage::query()
                ->with('station:id,latitude,longitude,locality_chinese,maximum_elevation,maximum_depth', 'project')
                ->where('record_id', $id)
                ->first();
            $record->date = Carbon::createFromFormat('Y-m-d H:i:s', $record->date)->format('Y-m-d');
        }

        return response()->json([
            'record' => $record,
        ]);

    }
}
