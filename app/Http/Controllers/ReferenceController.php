<?php

namespace App\Http\Controllers;

use App\Reference;
use Illuminate\Http\Request;

class ReferenceController extends Controller
{
    protected $perPage = 50;

    public function index(Request $request)
    {
        $citation = $request->get('citation');
        $year = $request->get('year');
        $tag = $request->get('tag');
        $sort = $request->get('sort');
        $direction = $request->get('direction');

        $referenceQuery = Reference::query();

        if ($citation) {
            $referenceQuery->where('citation', 'like', "%$citation%");
        }

        if ($year) {
            $referenceQuery->where('year', 'like', '%' . $year . '%');
        }

        if ($tag) {
            $referenceQuery->where('tags', 'like', "%$tag%");
        }

        if ($sort) {
            $referenceQuery->orderBy($sort, $direction);
        }

        $reference = $referenceQuery->paginate($this->perPage);

        return response()->json([
            'perPage' => $this->perPage,
            'total' => $reference->total(),
            'currentPage' => $reference->currentPage(),
            'data' => $reference->items(),
        ]);

    }
}
