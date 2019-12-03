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
        $familyName = $request->get('family_c');
        $class = $request->get('class');
        $className = $request->get('class_c');
        $phylum = $request->get('phylum');
        $phylumName = $request->get('phylum_c');
        $order = $request->get('order');
        $orderName = $request->get('order_c');
        $kingdom = $request->get('kingdom');
        $kingdomName = $request->get('kingdom_c');
        $author = $request->get('author');
        $scientificName = $request->get('scientific_name');
        $chineseName = $request->get('chinese');
        $sort = $request->get('sort');
        $direction = $request->get('direction', 'asc');

        $speciesQuery = Species::query();

        if ($phylum) {
            $speciesQuery->where('phylum', 'like', '%' . $phylum . '%');
        }

        if ($phylumName) {
            $speciesQuery->where('phylum_c', 'like', '%' . $phylumName . '%');
        }

        if ($kingdom) {
            $speciesQuery->where('kingdom_c', 'like', '%' . $kingdom . '%');
        }

        if ($kingdomName) {
            $speciesQuery->where('kingdom_c', 'like', '%' . $kingdomName . '%');
        }

        if ($class) {
            $speciesQuery->where('class', 'like', '%' . $class . '%');
        }

        if($className) {
            $speciesQuery->where('class_c', 'like', '%' . $className . '%');
        }

        if ($order) {
            $speciesQuery->where('order', 'like', '%' . $order . '%');
        }

        if ($orderName) {
            $speciesQuery->where('order_c', 'like', '%' . $order . '%');
        }

        if ($family) {
            $speciesQuery->where('family', 'like', '%' . $family . '%');
        }

        if ($familyName) {
            $speciesQuery->where('family_c', 'like', '%' . $familyName . '%');
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
