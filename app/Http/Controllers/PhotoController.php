<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function index()
    {
        $images = Storage::disk('images')->allFiles('/photos');
        return response()->json([
            'data' => $images
        ]);
    }
}
