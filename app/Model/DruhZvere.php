<?php

namespace App\Model;
// deklarace enumu
enum DruhZvere: int
{
    case SRNCI = 1;
    case DIVOCAK = 2;
    case JELEN = 3;
    case DANEK = 4;

    public static function fromValue(int $value): ?self
    {
        return self::tryFrom($value);
    }
 //metoda na prevod z int enumu na nazev
    public function getName(): string
    {
        return match ($this)
        {
            self::SRNCI => 'Srnčí',
            self::DIVOCAK => 'Divočák',
            self::JELEN => 'Jelen',
            self::DANEK => 'Daněk',
        };
    }
}
