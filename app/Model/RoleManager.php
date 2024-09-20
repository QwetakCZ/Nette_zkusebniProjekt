<?php

namespace App\Model;

use Nette\Database\Table\Selection;

class RoleManager extends BaseManager
{

    public function getTableName(): string
    {
        return 'role';
    }

    public function findByUserId(int $id): Selection
    {
        return $this->getAll()
            ->select('role.id, role.name')
            ->where(':user_x_role.user_id', $id)
            ->where(':user_x_role.role_id = role.id');
    }

    public function findByUserIdToSelect(int $id): array
    {
        return $this->findByUserId($id)
            ->fetchPairs('id', 'name');
    }
}