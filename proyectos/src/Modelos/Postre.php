<?php

namespace Cafeteria\Modelos;

class Postre extends Producto
{
    public function precioFinal(int $cantidad): float
    {
        $total = $this->precioBase * $cantidad;

        return $cantidad >= 3 ? $total * 0.90 : $total;
    }
}