<?php

namespace App\Http\Controllers;


use App\Services\RestaurantService;
class RestaurantController extends Controller
{

    protected $restaurantService;

    public function __construct(RestaurantService $restaurantService)
    {
        $this->restaurantService = $restaurantService;
    }

    public function index()
    {
        $restaurants = $this->restaurantService->getRestaurantsWithDetails();
        return response()->json($restaurants);
    }
}
