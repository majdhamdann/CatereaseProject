<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\District;
use App\Models\Area;

class LocationController extends Controller
{
      public function search(Request $request)
    {
        $search = $request->input('search');
        
        if (!$search) {
            $cities = City::with(['districts.areas'])
                ->orderBy('name')
                ->get();
            
            return response()->json([
                'data' => $cities,
                'message' => 'All cities with districts and areas'
            ]);
        }
        
        $results = [
            'cities' => [],
            'districts' => [],
            'areas' => []
        ];
        
        $cities = City::where('name', 'like', "%{$search}%")
            ->with(['districts.areas'])
            ->get();
        
        $districts = District::where('name', 'like', "%{$search}%")
            ->with(['city', 'areas'])
            ->get();
        
        $areas = Area::where('name', 'like', "%{$search}%")
            ->with(['district.city'])
            ->get();
        
        foreach ($cities as $city) {
            $results['cities'][] = [
                'id' => $city->id,
                'name' => $city->name,
                'country' => $city->country,
                'districts' => $city->districts->map(function($district) {
                    return [
                        'id' => $district->id,
                        'name' => $district->name,
                        'areas' => $district->areas
                    ];
                })
            ];
        }
        
        foreach ($districts as $district) {
            $results['districts'][] = [
                'id' => $district->id,
                'name' => $district->name,
                'city' => $district->city ? [
                    'id' => $district->city->id,
                    'name' => $district->city->name
                ] : null,
                'areas' => $district->areas
            ];
        }
        
        foreach ($areas as $area) {
            $results['areas'][] = [
                'id' => $area->id,
                'name' => $area->name,
                'district' => $area->district ? [
                    'id' => $area->district->id,
                    'name' => $area->district->name
                ] : null,
                'city' => $area->district && $area->district->city ? [
                    'id' => $area->district->city->id,
                    'name' => $area->district->city->name
                ] : null
            ];
        }
        
        return response()->json([
            'search_term' => $search,
            'results' => $results,
           
        ]);
    }
    
}
