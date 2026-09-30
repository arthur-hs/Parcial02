<?php

namespace Cafeteria\Enums;

enum Tamano: string
{
    case Pequeno = 'P';
    case Mediano = 'M';
    case Grande = 'G';

    public function recargo(): float
    {
        return match ($this) {
            self::Pequeno => 0.00,
            self::Mediano => 0.25,
            self::Grande => 0.50,
        };
    }
}