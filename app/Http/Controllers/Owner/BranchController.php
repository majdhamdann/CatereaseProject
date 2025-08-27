<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchServiceType;
use App\Models\Category;
use App\Models\City;
use App\Models\Restaurant;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchController extends Controller
{
    public function showRestaurantDetails()
{
    $user = Auth::user();
    $this->ensureIsRestaurantOwner($user);

    $restaurant = Restaurant::with([
        'branches' => function($query) {
            $query->with([
                'deliveryAreas',
                'foodCategories',
                'workingDays',
                'branchServiceTypes.serviceType', 
                'packages' => function($query) {
                    $query->with([
                        'extras',
                        'categories',
                        'occasionTypes',
                        'discounts'
                    ]);
                },
                'deliveryPeople',
                'city',
                'feedbacks',
                'categories' ,
                'owner'
            ]);
        },
    ])->where('owner_id', $user->id)->first();

    if (!$restaurant) {
        return response()->json(['error' => 'You do not own a restaurant.'], 404);
    }

  

    return response()->json([
        'restaurant' => [
            'basic_info' => [
                'id_restaurant' => $restaurant->id,
                'name' => $restaurant->name,
                'photo' => $restaurant->photo,
                'owner' => $restaurant->owner->name,
                'description' => $restaurant->description
            ],
            'branches' => $restaurant->branches->map(function($branch) {
                return [
                    'branch_info' => [
                        'id' => $branch->id,
                        'manager' => $branch->manager->name,
                        'description' => $branch->description,
                        'photo' => $branch->photo,
                        'location_note' => $branch->location_note,
                        'city' => $branch->city->name
                    ],
                    'delivery_areas' => $branch->deliveryAreas->map(function($area) {
                        return [
                            'delivery_city' => $area->city->name,
                            'delivery_distract' => $area->district->name,
                            'delivery_price' => $area->delivery_price
                        ];
                    }),
                    'working_hours' => $branch->workingDays,
                    'food_categories' => $branch->foodCategories->map(function($category) {
                        return [
                            'name' => $category->category->name,
                            
                        ];
                    }),
                    'services' => $branch->branchServiceTypes->map(function($service) {
                        return [
                            'name' => $service->serviceType->name,
                            'custom_price' => $service->custom_price,
                            'service_cost' => $service->service_cost
                        ];
                    }),
                    'packages' => $branch->packages->map(function($package) {
                        return [
                            'name' => $package->name,
                            'price' => $package->base_price,
                            'occasions' => $package->occasionTypes->pluck('name')
                        ];
                    })
                ];
            })
        ]
    ]);
}
    
    public function index()
    {
        $user = Auth::user();
        $this->ensureIsRestaurantOwner($user);

        $role = $user->role->name ?? null;

        $branches = $role === 'Admin'
            ? Branch::all()
            : $user->restaurant->branches()->get();

        return response()->json([
            'branches' => $branches
        ]);
    }

    private function ensureIsRestaurantOwner($user)
    {
        $role = $user->role->name ?? null;

        if ($role !== 'Admin' && !$user->restaurant) {
            abort(403, 'Only restaurant owners or admins can perform this action');
        }
    }

   public function store(Request $request)
{
    $user = Auth::user();
    $this->ensureIsRestaurantOwner($user);
    
    $role = $user->role->name ?? null;
    $validationRules = [
        'description' => 'required|string',
        'location_note' => 'required|string',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'city_id' => 'required|exists:cities,id',
        
        'manager_id' => 'nullable|exists:users,id',
        'categories' => 'required|array',
        'categories.*' => 'string', 
        'working_hours' => 'required|array',
        'working_hours.*.day' => 'required|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
        'working_hours.*.open_time' => 'nullable|date_format:H:i',
        'working_hours.*.close_time' => 'nullable|date_format:H:i',
        'services' => 'required|array',
        'services.*.name' => 'required|string', 
        'services.*.price' => 'required|numeric',
        'services.*.description' => 'nullable|string',
        'delivery_regions' => 'nullable|array',
        'delivery_regions.*.city_id' => 'required|exists:cities,id',
        'delivery_regions.*.district_id' => 'required|exists:districts,id',
        'delivery_regions.*.delivery_price' => 'required|numeric',
       // 'delivery_regions.*.description' => 'nullable|string',
    ];

    if ($role === 'Admin') {
        $validationRules['restaurant_id'] = 'required|exists:restaurants,id';
    }

    $data = $request->validate($validationRules);

    if ($role !== 'Admin') {
        $data['restaurant_id'] = $user->restaurant->id;
    }

    $branch = Branch::create($data);

    if (isset($data['categories'])) {
        $categoryIds = [];
        foreach ($data['categories'] as $categoryName) {
            $category = Category::firstOrCreate(
                ['name' => $categoryName],
                ['description' => 'Auto-created category']
            );
            $categoryIds[] = $category->id;
        }
        $branch->categories()->sync($categoryIds);
    }

    if (isset($data['services'])) {
        foreach ($data['services'] as $service) {
            $serviceType = ServiceType::firstOrCreate(
                ['name' => $service['name']],
                [
                    'description' => $service['description'] ?? 'Auto-created service',
                    'pricing_model' => 'fixed',
                    'is_active' => true
                ]
            );
            
            $branch->branchServiceTypes()->create([
                'service_type_id' => $serviceType->id,
                'custom_price' => $service['price'],
                'service_cost' => 0,
                'description' => $service['description'] ?? null,
                'is_available' => true
            ]);
        }
    }

    if (isset($data['working_hours'])) {
        foreach ($data['working_hours'] as $workingHour) {
            $branch->workingDays()->create([
                'day_of_week' => $workingHour['day'],
                'open_time' => $workingHour['open_time'] ?? null,
                'close_time' => $workingHour['close_time'] ?? null,
                'is_closed' => ($workingHour['open_time'] === null && $workingHour['close_time'] === null),
            ]);
        }
    }

    if (isset($data['delivery_regions'])) {
        foreach ($data['delivery_regions'] as $region) {
            $branch->deliveryAreas()->create([
                'city_id' => $region['city_id'],
                 'district_id' => $region['district_id']?? null,
                'delivery_price' => $region['delivery_price'],
               // 'description' => $region['description'] ?? null,
            ]);
        }
    }
    return response()->json([
        'message' => 'Branch created successfully',
        'branch' => $branch->load([
            'categories',
            'workingDays',
            'branchServiceTypes.serviceType',
            'deliveryAreas.city',
            'deliveryAreas.district'
        ])
    ], 201);
}
  public function getCity(){
    return City::all();
  }
 public function addCategoriesToBranch(Request $request, $branchId)
    {
        $user = Auth::user();
        $this->ensureIsRestaurantOwner($user);

        $role = $user->role->name ?? null;

        $branch = $role === 'Admin'
            ? Branch::find($branchId)
            : Branch::where('id', $branchId)->where('restaurant_id', $user->restaurant->id)->first();

        if (!$branch) {
            return response()->json(['error' => 'Branch not found or unauthorized'], 404);
        }

        $data = $request->validate([
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
        ]);

        $branch->categories()->syncWithoutDetaching($data['category_ids']);

        return response()->json([
            'message' => 'Categories linked to branch successfully',
            'branch' => $branch->load('categories')
        ]);
    }

    
public function show($id)
{
    $branch = Branch::with([
        'restaurant',
        'manager',
        'city',
        'categories',
        'workingDays',
        'branchServiceTypes.serviceType',
        'deliveryAreas.city',
         'deliveryAreas.district', 
        
    ])->findOrFail($id);

    return response()->json([
        'success' => true,
        'branch' => $branch
    ]);
}
    public function update(Request $request, $id)
{
    $user = Auth::user();
    $this->ensureIsRestaurantOwner($user);
    
    $branch = Branch::findOrFail($id);
    $this->ensureUserCanModifyBranch($user, $branch);

    $role = $user->role->name ?? null;

    $validationRules = [
        'description' => 'sometimes|required|string',
        'location_note' => 'sometimes|required|string',
        'latitude' => 'sometimes|nullable|numeric',
        'longitude' => 'sometimes|nullable|numeric',
        'city_id' => 'sometimes|required|exists:cities,id',
        'manager_id' => 'sometimes|nullable|exists:users,id',
        'categories' => 'sometimes|required|array',
        'categories.*' => 'string',
        'working_hours' => 'sometimes|required|array',
        'working_hours.*.id' => 'sometimes|exists:working_days,id,branch_id,'.$id,
        'working_hours.*.day' => 'required_with:working_hours|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
        'working_hours.*.open_time' => 'nullable|date_format:H:i',
        'working_hours.*.close_time' => 'nullable|date_format:H:i',
        'services' => 'sometimes|required|array',
        'services.*.id' => 'sometimes|exists:branch_service_types,id,branch_id,'.$id,
        'services.*.name' => 'required_with:services|string',
        'services.*.price' => 'required_with:services|numeric',
        'services.*.description' => 'nullable|string',
        'delivery_regions' => 'sometimes|nullable|array',
        'delivery_regions.*.id' => 'sometimes|exists:branch_delivery_areas,id,branch_id,'.$id,
        'delivery_regions.*.city_id' => 'required_with:delivery_regions|exists:cities,id',
        'delivery_regions.*.district_id' => 'nullable|exists:districts,id',
        'delivery_regions.*.delivery_price' => 'required_with:delivery_regions|numeric', 

        'delivery_regions.*.description' => 'nullable|string',
    ];

    if ($role === 'Admin') {
        $validationRules['restaurant_id'] = 'sometimes|required|exists:restaurants,id';
    }

    $data = $request->validate($validationRules);

    $branch->update($data);

    if (isset($data['categories'])) {
        $categoryIds = [];
        foreach ($data['categories'] as $categoryName) {
            $category = Category::firstOrCreate(
                ['name' => $categoryName],
                ['description' => 'Auto-created category']
            );
            $categoryIds[] = $category->id;
        }
        $branch->categories()->sync($categoryIds);
    }

    if (isset($data['working_hours'])) {
        $existingWorkingDayIds = $branch->workingDays()->pluck('id')->toArray();
        $updatedWorkingDayIds = [];
        
        foreach ($data['working_hours'] as $workingHour) {
            if (isset($workingHour['id'])) {
                $branch->workingDays()
                    ->where('id', $workingHour['id'])
                    ->update([
                        'day_of_week' => $workingHour['day'],
                        'open_time' => $workingHour['open_time'],
                        'close_time' => $workingHour['close_time'],
                        'is_closed' => empty($workingHour['open_time']) && empty($workingHour['close_time'])
                    ]);
                $updatedWorkingDayIds[] = $workingHour['id'];
            } else {
                $newWorkingDay = $branch->workingDays()->create([
                    'day_of_week' => $workingHour['day'],
                    'open_time' => $workingHour['open_time'],
                    'close_time' => $workingHour['close_time'],
                    'is_closed' => empty($workingHour['open_time']) && empty($workingHour['close_time'])
                ]);
                $updatedWorkingDayIds[] = $newWorkingDay->id;
            }
        }
        
        $toDelete = array_diff($existingWorkingDayIds, $updatedWorkingDayIds);
        if (!empty($toDelete)) {
            $branch->workingDays()->whereIn('id', $toDelete)->delete();
        }
    }

    if (isset($data['services'])) {
        $existingServiceIds = $branch->branchServiceTypes()->pluck('id')->toArray();
        $updatedServiceIds = [];
        
        foreach ($data['services'] as $service) {
            $serviceType = ServiceType::firstOrCreate(
                ['name' => $service['name']],
                [
                    'description' => $service['description'] ?? 'Auto-created service',
                    'pricing_model' => 'fixed',
                    'is_active' => true
                ]
            );
            
            if (isset($service['id'])) {
                $branch->branchServiceTypes()
                    ->where('id', $service['id'])
                    ->update([
                        'service_type_id' => $serviceType->id,
                        'custom_price' => $service['price'],
                        'description' => $service['description'] ?? null,
                        'is_available' => true
                    ]);
                $updatedServiceIds[] = $service['id'];
            } else {
                $newService = $branch->branchServiceTypes()->create([
                    'service_type_id' => $serviceType->id,
                    'custom_price' => $service['price'],
                    'service_cost' => 0,
                    'description' => $service['description'] ?? null,
                    'is_available' => true
                ]);
                $updatedServiceIds[] = $newService->id;
            }
        }
        
        $toDelete = array_diff($existingServiceIds, $updatedServiceIds);
        if (!empty($toDelete)) {
            $branch->branchServiceTypes()->whereIn('id', $toDelete)->delete();
        }
    }

 if (isset($data['delivery_regions'])) {
    $existingRegionIds = $branch->deliveryAreas()->pluck('id')->toArray();
    $updatedRegionIds = [];
    
    foreach ($data['delivery_regions'] as $region) {
        $regionData = [
            'city_id' => $region['city_id'],
            'district_id' => $region['district_id'] ?? null,
            'delivery_price' => $region['delivery_price'],
        ];

        $existingRegion = $branch->deliveryAreas()
            ->where('city_id', $region['city_id'])
            ->where('district_id', $region['district_id'] ?? null)
            ->first();

        if ($existingRegion) {
            $existingRegion->update(['delivery_price' => $region['delivery_price']]);
            $updatedRegionIds[] = $existingRegion->id;
        } else {
            $newRegion = $branch->deliveryAreas()->create($regionData);
            $updatedRegionIds[] = $newRegion->id;
        }
    }
    
    $toDelete = array_diff($existingRegionIds, $updatedRegionIds);
    if (!empty($toDelete)) {
        $branch->deliveryAreas()->whereIn('id', $toDelete)->delete();
    }
}
    return response()->json([
        'message' => 'Branch updated successfully',
        'branch' => $branch->load([
            'categories',
            'workingDays',
            'branchServiceTypes.serviceType',
            'deliveryAreas.city',
            'deliveryAreas.district'
        ])
    ]);
}

private function ensureUserCanModifyBranch($user, $branch)
{
    if ($user->role->name !== 'Admin' && $branch->restaurant_id !== $user->restaurant->id) {
        abort(403, 'Unauthorized action.');
    }
}

    public function destroy($id)
    {
        $user = Auth::user();
        $this->ensureIsRestaurantOwner($user);

        $role = $user->role->name ?? null;

        $branch = $role === 'Admin'
            ? Branch::findOrFail($id)
            : Branch::where('id', $id)->where('restaurant_id', $user->restaurant->id)->firstOrFail();

        $branch->delete();

        return response()->json(['message' => 'Branch deleted']);
    }
      public function getOwnerBranchesWithPackages()
{
    $owner = auth()->user(); 

    $branches = Branch::with([
        'packages.categories',           
        'packages.extraServices.serviceType', 
        'packages.occasionTypes',        
        'branchServiceTypes.serviceType' ,
        'deliveryAreas.city'  
    ])
    ->whereHas('restaurant', function ($q) use ($owner) {
        $q->where('owner_id', $owner->id);
    })
    ->get();

    return response()->json([
        'status' => true,
        'data' => $branches
    ]);
}
public function getOwnerBranches()
{
    $owner = Auth::user();
    $this->ensureIsRestaurantOwner($owner);

    $branches = Branch::with(['restaurant.owner', 'city'])
        ->whereHas('restaurant', function($query) use ($owner) {
            $query->where('owner_id', $owner->id);
        })
        ->get()
        ->map(function($branch) {
            return [
                'id' => $branch->id,
                'name' => $branch->location_note ?? $branch->description ?? 'بدون اسم',
                'image' => $branch->photo, 
                'ownerName' => $branch->restaurant->owner->name ?? 'غير معروف',
                'Manager' => $branch->manager->name ?? 'غير معروف',
                'city' => $branch->city->name ?? 'غير معروفة'
            ];
        });

    return response()->json([
        'status' => true,
        'branches' => $branches
    ]);
}
}
