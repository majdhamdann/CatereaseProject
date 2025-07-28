<?php

namespace App\Services\Manager;

use App\Models\Branch;
use App\Models\BranchServiceType;
use Illuminate\Support\Facades\Auth;

class BranchServiceTypeService
{
    public function getAllForManager()
    {
        $managerId = Auth::id();

        return BranchServiceType::whereHas('branch', function ($query) use ($managerId) {
            $query->where('manager_id', $managerId);
        })->with('branch')->get();
    }

    public function create(array $data)
    {
        $managerId = Auth::id();

        $branch = Branch::where('id', $data['branch_id'])
            ->where('manager_id', $managerId)
            ->first();

        if (!$branch) {
            return null;
        }

        return BranchServiceType::create($data);
    }

    public function getByIdForManager($id)
    {
        $managerId = Auth::id();

        return BranchServiceType::where('id', $id)
            ->whereHas('branch', function ($query) use ($managerId) {
                $query->where('manager_id', $managerId);
            })
            ->first();
    }

    public function update($id, array $data)
    {
        $item = $this->getByIdForManager($id);

        if (!$item) {
            return null;
        }

        $item->update($data);

        return $item;
    }

    public function delete($id)
    {
        $item = $this->getByIdForManager($id);

        if (!$item) {
            return false;
        }

        $item->delete();

        return true;
    }
}
