<?php


namespace App\Repositories\Contracts;

interface AddressRepositoryInterface
{
    public function create(array $data);
    public function getByUserId($userId);
    public function delete($id);
    public function update($id, array $data);
    public function findByIdAndUser($id, $userId);
    public function unsetDefaultForUser($userId);



}
