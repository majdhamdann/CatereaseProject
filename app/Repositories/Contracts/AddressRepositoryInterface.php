<?php


namespace App\Repositories\Contracts;

interface AddressRepositoryInterface
{
    public function create(array $data);
    public function getByUserId($userId);
    public function delete($id);
}
