<?php

use Cafeteria\Enums\Tamano;

$escapar = static fn (string $valor): string =>
    htmlspecialchars($valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cafetería universitaria</title>
    <link rel="stylesheet" href="css/app.css">
</head>
<body>
<main>
    <h1>Cafetería universitaria</h1>

    <p><a href="listar.php">Ver pedidos</a></p>

    <?php if ($error !== null): ?>
        <p class="error" role="alert">
            <?= $escapar($error) ?>
        </p>
    <?php endif; ?>

    <form action="index.php" method="post">
        <div>
            <label for="cliente">Cliente</label>
            <input
                id="cliente"
                name="cliente"
                value="<?= $escapar($datos['cliente']) ?>"
                required
            >
        </div>

        <div>
            <label for="nombre">Nombre del producto</label>
            <input
                id="nombre"
                name="nombre"
                value="<?= $escapar($datos['nombre']) ?>"
                required
            >
        </div>

        <fieldset class="completo">
            <legend>Tipo de producto</legend>

            <label>
                <input
                    type="radio"
                    name="tipo"
                    value="bebida"
                    <?= $datos['tipo'] === 'bebida' ? 'checked' : '' ?>
                    required
                >
                Bebida
            </label>

            <label>
                <input
                    type="radio"
                    name="tipo"
                    value="postre"
                    <?= $datos['tipo'] === 'postre' ? 'checked' : '' ?>
                >
                Postre
            </label>
        </fieldset>

        <div>
            <label for="precioBase">Precio base ($)</label>
            <input
                id="precioBase"
                type="number"
                name="precioBase"
                min="0.01"
                step="0.01"
                value="<?= $escapar($datos['precioBase']) ?>"
                required
            >
        </div>

        <div>
            <label for="cantidad">Cantidad</label>
            <input
                id="cantidad"
                type="number"
                name="cantidad"
                min="1"
                max="20"
                step="1"
                value="<?= $escapar($datos['cantidad']) ?>"
                required
            >
        </div>

        <div class="completo">
            <label for="tamano">Tamaño</label>

            <select id="tamano" name="tamano" required>
                <?php foreach (Tamano::cases() as $tamano): ?>
                    <option
                        value="<?= $escapar($tamano->value) ?>"
                        <?= $datos['tamano'] === $tamano->value
                            ? 'selected' : '' ?>
                    >
                        <?= $tamano === Tamano::Pequeno
                            ? 'Pequeño' : $escapar($tamano->name) ?>
                        (+$<?= number_format($tamano->recargo(), 2) ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <small>El tamaño solo se aplica a las bebidas.</small>
        </div>

        <button type="submit" class="completo">
            Registrar pedido
        </button>
    </form>
</main>
</body>
</html>