<?php

namespace Cafeteria\Controladores;

use Cafeteria\Enums\Tamano;
use Cafeteria\Excepciones\PedidoInvalidoException;
use Cafeteria\Modelos\Producto;
use Cafeteria\Modelos\Bebida;
use Cafeteria\Modelos\Postre;

class PedidoControlador
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function inicio(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->calcular();
        } else {
            $this->formulario();
        }
    }

    public function formulario(
        array $datos = [],
        ?string $error = null
    ): void {
        $datos = array_merge([
            'cliente' => '',
            'tipo' => 'bebida',
            'nombre' => '',
            'precioBase' => '',
            'cantidad' => '1',
            'tamano' => 'P',
        ], $datos);

        require __DIR__ . '/../../vistas/formulario.php';
    }

    public function calcular(): void
    {
        $datos = [];
        $formatoInvalido = false;

        foreach (
            ['cliente', 'tipo', 'nombre', 'precioBase', 'cantidad', 'tamano']
            as $campo
        ) {
            $valor = $_POST[$campo] ?? '';

            if (!is_string($valor)) {
                $formatoInvalido = true;
                $valor = '';
            }

            $datos[$campo] = trim($valor);
        }

        try {
            if ($formatoInvalido) {
                throw new PedidoInvalidoException(
                    'El formulario contiene datos inválidos.'
                );
            }

            if ($datos['cliente'] === '' || $datos['nombre'] === '') {
                throw new PedidoInvalidoException(
                    'El cliente y el producto son obligatorios.'
                );
            }

            if (!in_array($datos['tipo'], ['bebida', 'postre'], true)) {
                throw new PedidoInvalidoException(
                    'Selecciona bebida o postre.'
                );
            }

            $precio = filter_var(
                $datos['precioBase'],
                FILTER_VALIDATE_FLOAT
            );

            if ($precio === false || !is_finite($precio) || $precio <= 0) {
                throw new PedidoInvalidoException(
                    'El precio debe ser mayor que cero.'
                );
            }

            $cantidad = filter_var(
                $datos['cantidad'],
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1, 'max_range' => 20]]
            );

            if ($cantidad === false) {
                throw new PedidoInvalidoException(
                    'La cantidad debe ser un entero entre 1 y 20.'
                );
            }

            if (Tamano::tryFrom($datos['tamano']) === null) {
                throw new PedidoInvalidoException(
                    'El tamaño seleccionado no es válido.'
                );
            }

            $producto = $datos['tipo'] === 'bebida'
                ? new Bebida(
                    $datos['nombre'],
                    $precio,
                    Tamano::from($datos['tamano'])
                )
                : new Postre($datos['nombre'], $precio);

            $this->registrar($producto, $datos['cliente'], $cantidad);

            header('Location: listar.php', true, 303);
            exit;
        } catch (PedidoInvalidoException $e) {
            $this->formulario($datos, $e->getMessage());
        }
    }

    private function registrar(
        Producto $producto,
        string $cliente,
        int $cantidad
    ): void {
        $total = $producto->precioFinal($cantidad);

        if (!is_finite($total)) {
            throw new PedidoInvalidoException(
                'El precio ingresado es demasiado grande.'
            );
        }

        $_SESSION['pedidos'][] = [
            'cliente' => $cliente,
            'producto' => $producto->nombre,
            'cantidad' => $cantidad,
            'total' => $total,
        ];
    }

    public function listar(): void
    {
        $pedidos = $_SESSION['pedidos'] ?? [];

        require __DIR__ . '/../../vistas/listado.php';
    }
}