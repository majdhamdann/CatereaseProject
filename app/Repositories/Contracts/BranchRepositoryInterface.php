<?php

namespace App\Repositories\Contracts;

interface BranchRepositoryInterface
{
    public function getAllWithRelations();

    public function getNearby($lat, $lng);

    public function getByCityId($cityId);

    public function getAvailableItemsByBranch($branchId);

}
