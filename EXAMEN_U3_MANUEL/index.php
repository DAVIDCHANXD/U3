<?php
session_start();
$estaLogueado = isset($_SESSION['usuario_email']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio – Inventario</title>
    <link rel="stylesheet" href="CSS/index.css">
</head>
<body>
<div class="container">
    <h1>Sistema de Inventario – Salud Total</h1>
    <p>Selecciona qué quieres hacer:</p>

    <div class="user-info">
        <?php if ($estaLogueado): ?>
            Sesión iniciada como
            <strong><?php echo htmlspecialchars($_SESSION['usuario_email']); ?></strong>.
        <?php else: ?>
            <p>No has iniciado sesión.</p>
            <p class="nota">
                ⚠ Para ver el <strong>panel de inventario</strong> primero debes iniciar sesión
                usando el botón <strong>"Iniciar sesión"</strong> de abajo.
            </p>
        <?php endif; ?>
    </div>

    <div class="buttons">
        <!-- Botón 1: Ver panel -->
        <a
            href="<?php echo $estaLogueado ? 'panel.php' : '#'; ?>"
            class="btn btn-panel"
            id="btnVerPanel"
            data-logueado="<?php echo $estaLogueado ? '1' : '0'; ?>"
        >
            Ver panel de inventario
        </a>

        <!-- Botón 2: Iniciar sesión -->
        <a href="login.php" class="btn btn-login">
            Iniciar sesión
        </a>
    </div>
</div>

<script src="JS/app.js"></script>
</body>
</html>
