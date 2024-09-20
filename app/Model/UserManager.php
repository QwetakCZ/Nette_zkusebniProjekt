<?php

namespace App\Model;

use Exception;
use Nette\Database\Explorer;
use Nette\Database\ForeignKeyConstraintViolationException;
use Nette\Database\Table\ActiveRow;
use Nette\Security\Passwords;

class UserManager extends BaseManager
{
    private Passwords $passwords;
    public function __construct(Explorer $db, Passwords $passwords)
    {
        $this->passwords = $passwords;
        parent::__construct($db);

    }

    public function getTableName(): string
    {
        return 'user';
    }
    public function getByEmail(string $email): ?ActiveRow
    {
        return $this->getAll()->where('email', $email)->fetch();
    }

    /**
     * Přidá nového uživatele do databáze
     *
     * @param string $username
     * @param string $email
     * @param string $password
     * @throws Exception
     */
    public function add(string $username, string $email, string $password): void
    {
        $hash = $this->passwords->hash($password);

        try {
            $this->getAll()->insert([
                'username' => $username,
                'email' => $email,
                'password' => $hash,
                'userId' => uniqid(),
            ]);
        } catch (ForeignKeyConstraintViolationException $e) {
            throw new Exception('Uživatelské jméno nebo email již existuje.');
        }
    }
}