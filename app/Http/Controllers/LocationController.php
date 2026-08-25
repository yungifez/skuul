<?php

namespace App\Http\Controllers;

use App\Services\Location\LocationCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Return the states for a country.
     */
    public function states(Request $request, LocationCatalog $locationCatalog): JsonResponse
    {
        return response()->json(
            $locationCatalog->statesForCountry($request->string('country')->trim()->toString()),
        );
    }

    /**
     * Return the cities for a country.
     */
    public function cities(Request $request, LocationCatalog $locationCatalog): JsonResponse
    {
        return response()->json(
            $locationCatalog->citiesForCountry($request->string('country')->trim()->toString()),
        );
    }
}
