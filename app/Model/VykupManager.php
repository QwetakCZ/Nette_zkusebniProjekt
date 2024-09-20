<?php

namespace App\Model;

use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;

class VykupManager extends BaseManager
{

    public function getTableName(): string
    {
        return 'vykup';
    }

    public function findAllVykupy(string $userId): Selection
    {
        return $this->getAll()
            ->where('userId', $userId);
    }

    public function getVykupById(int $id, string $userId): ?ActiveRow
    {
        return $this->getAll()
            ->where('id', $id)
            ->where('userId', $userId)
            ->fetch();
    }

    public function createVykup(array $values): void
    {
        $this->getAll()->insert($values);
    }

    public function updateVykup(int $id, string $userId, array $values): void
    {
        $this->getAll()
            ->where('id', $id)
            ->where('userId', $userId)
            ->update([
               'plomba' => $values['plomba'],
               'vaha' => $values['vaha'],
               'dodavatel_id' => $values['dodavatel_id'],
                'prodejniCena' => $values['prodejniCena'],
            ]);
    }

    public function deleteVykup(int $id, string $userId): bool
    {
        try {
            $rowsAffected = $this->getAll()
                ->where('id', $id)
                ->where('userId', $userId)
                ->delete();

            // dostaneme true pokud byl smazan min. jeden radek
            return $rowsAffected > 0;
            }
            catch (\Exception)
            {
                return false;
            }

    }
}