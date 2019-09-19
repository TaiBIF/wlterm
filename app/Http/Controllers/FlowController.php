<?php

namespace App\Http\Controllers;

use App\Flow;

class FlowController extends Controller
{
    protected $perPage = 50;

    public function index()
    {
        $flowRecords = Flow::query()
            ->with(['rain', 'station'])
            ->paginate($this->perPage);

        return response()->json([
            'total' => $flowRecords->total(),
            'current_page' => $flowRecords->currentPage(),
            'data' => $flowRecords->items(),
        ]);
    }
}
