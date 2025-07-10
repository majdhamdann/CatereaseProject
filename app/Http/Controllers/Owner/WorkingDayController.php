<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\WorkingDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkingDayController extends Controller
{
   private function ensureBranchBelongsToOwner($branchId)
{
    $user = Auth::user();
    $role = $user->role->name ?? null;

    if ($role === 'Admin') {
        $branch = Branch::find($branchId);
    } elseif ($user->restaurant) {
        $branch = Branch::where('id', $branchId)
            ->where('restaurant_id', $user->restaurant->id)
            ->first();
    } else {
        abort(403, 'Unauthorized: No restaurant assigned to you.');
    }

    if (!$branch) {
        abort(403, 'You are not authorized to access this branch.');
    }

    return $branch;
}


    public function index($branchId)
    {
        $this->ensureBranchBelongsToOwner($branchId);

        $workingDays = WorkingDay::where('branch_id', $branchId)->get();

        return response()->json($workingDays);
    }

    public function store(Request $request, $branchId)
    {
        $this->ensureBranchBelongsToOwner($branchId);

        $data = $request->validate([
            'day_of_week' => 'required|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
            'open_time' => 'nullable|date_format:H:i',
            'close_time' => 'nullable|date_format:H:i',
            'is_closed' => 'required|boolean',
        ]);

        $data['branch_id'] = $branchId;

        $workingDay = WorkingDay::create($data);

        return response()->json(['message' => 'Working day created', 'data' => $workingDay]);
    }

    public function update(Request $request, $id)
    {
        $workingDay = WorkingDay::findOrFail($id);
        $this->ensureBranchBelongsToOwner($workingDay->branch_id);

        $data = $request->validate([
            'day_of_week' => 'required|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
            'open_time' => 'nullable|date_format:H:i',
            'close_time' => 'nullable|date_format:H:i',
            'is_closed' => 'required|boolean',
        ]);

        $workingDay->update($data);

        return response()->json(['message' => 'Working day updated', 'data' => $workingDay]);
    }

    public function destroy($id)
    {
        $workingDay = WorkingDay::findOrFail($id);
        $this->ensureBranchBelongsToOwner($workingDay->branch_id);

        $workingDay->delete();

        return response()->json(['message' => 'Working day deleted']);
    }
}
