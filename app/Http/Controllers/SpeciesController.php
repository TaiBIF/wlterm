<?php

namespace App\Http\Controllers;

use App\Species;
use Illuminate\Http\Request;

class SpeciesController extends Controller
{
    protected $perPage = 50;

    public function index(Request $request)
    {
        $family = $request->get('family');
        $class = $request->get('class');
        $phylum = $request->get('phylum');
        $order = $request->get('order');
        $kingdom = $request->get('kingdom');
        $author = $request->get('author');
        $scientificName = $request->get('scientificName');
        $chineseName = $request->get('chineseName');
        $sort = $request->get('sort');
        $direction = $request->get('direction', 'asc');

        $speciesQuery = Species::query();

        if ($phylum) {
            $speciesQuery->where(function($query) use ($phylum) {
                $query->where('phylum', 'like', '%' . $phylum . '%')
                    ->orWhere('phylum_c', 'like', '%' . $phylum . '%');
            });
        }

        if ($kingdom) {
            $speciesQuery->where(function($query) use ($kingdom) {
                $query->where('kingdom_c', 'like', '%' . $kingdom . '%')
                    ->orWhere('kingdom', 'like', '%' . $kingdom . '%');
            });
        }

        if ($class) {
            $speciesQuery->where(function($query) use ($class) {
                $query->where('class_c', 'like', '%' . $class . '%')
                    ->orWhere('class', 'like', '%' . $class . '%');
            });
        }

        if ($order) {
            $speciesQuery->where(function($query) use ($order) {
                $query->where('order_c', 'like', '%' . $order . '%')
                    ->orWhere('order', 'like', '%' . $order . '%');
            });
        }

        if ($family) {
            $speciesQuery->where(function($query) use ($family) {
                $query->where('family_c', 'like', '%' . $family . '%')
                    ->orWhere('family', 'like', '%' . $family . '%');
            });
        }

        if ($author) {
            $speciesQuery->where('author', 'like', '%' . $author . '%');
        }

        if ($scientificName) {
            $speciesQuery->where('scientific_name', 'like', '%' . $scientificName . '%');
        }

        if ($chineseName) {
            $speciesQuery->where('chinese', 'like', '%' . $chineseName . '%');
        }

        if ($sort && $direction) {
            $speciesQuery->orderBy($sort, $direction);
        }

        $species = $speciesQuery->paginate($this->perPage);

        return response()->json([
            'total' => $species->total(),
            'currentPage' => $species->currentPage(),
            'data' => $species->items(),
        ]);
    }
}
