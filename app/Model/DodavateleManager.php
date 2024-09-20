<?php

namespace App\Model;

use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;

class DodavateleManager extends BaseManager
{

    public function getTableName(): string
    {
        return 'dodavatele';
    }

    public function findAllDodavatele(string $userId): Selection
    {
        return $this->getAll()
            ->where('userId', $userId);
    }

    public function findDodavatelById(int $id, string $userId): ?ActiveRow
    {
        return $this->getAll()
            ->where('id', $id)
            ->where('userId', $userId)
            ->fetch();
    }

    public function createDodavatel(array $values): void
    {
        $this->getAll()->insert($values);
    }

    public function updateDodavatel(int $id, string $userId, array $values): void
    {
        $this->getAll()
            ->where('id', $id)
            ->where('userId', $userId)
            ->update([
               'nazev' => $values['nazev'],
               'ico' => $values['ico'],
               'telefon' => $values['telefon'],
            ]);
    }

    public function deleteDodavatel(int $id, string $userId): bool
    {
        try {
            $rowsAffected = $this->getAll()
                ->where('id', $id)
                ->where('userId', $userId)
                ->delete();

            // true kdyz byl smazan aspon jeden radek
            return $rowsAffected > 0;
            }
            catch (\Exception)
            {
                return false;
            }

    }
}