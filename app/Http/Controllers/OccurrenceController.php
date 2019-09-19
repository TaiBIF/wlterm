<?php

namespace App\Http\Controllers;

use App\Occurrence;
use Illuminate\Http\Request;

class OccurrenceController extends Controller
{
    protected $perPage = 50;

    public function index(Request $request)
    {
        $family = $request->get('family');
        $class = $request->get('class');
        $phylum = $request->get('phylum');
        $order = $request->get('order');
        $author = $request->get('author');
        $date = $request->get('date');
        $scientificName = $request->get('scientific_name');
        $chineseName = $request->get('chinese');
        $locality = $request->get('locality_chinese');
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
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

        if ($author) {
            $occurrencesQuery->where('author', 'like', '%' . $author . '%');
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
        }

        $occurrencesPage = $occurrencesQuery->paginate($this->perPage);
        return response()->json([
            'total' => $occurrencesPage->total(),
            'currentPage' => $occurrencesPage->currentPage(),
            'data' => $occurrencesPage->items(),
        ]);
    }
}
