<?php
namespace Cafeteria\Modelos;
abstract class Producto{
    public function __construct(
        public readonly string $nombre,
        public readonly float $precioBase
    )
    {}
    abstract public function precioFinal(int $cantidad): float;
}
