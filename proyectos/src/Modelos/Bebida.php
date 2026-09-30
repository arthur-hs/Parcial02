<?php

namespace Cafeteria\Modelos;

use Cafeteria\Enums\Tamano;

class Bebida extends Producto
{
    public function __construct(
        string $nombre,
        float $precioBase,
        public readonly Tamano $tamano
    ) {
        parent::__construct($nombre, $precioBase);
    }

    public function precioFinal(int $cantidad): float
    {
        return ($this->precioBase + $this->tamano->recargo())
            * $cantidad;
    }
}