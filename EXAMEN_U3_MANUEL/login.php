<?php
session_start();

// Incluimos la conexión PDO
require_once __DIR__ . '/conexion.php';

$mensajeError = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $clave = trim($_POST['clave'] ?? '');

    if ($email === '' || $clave === '') {
        $mensajeError = 'Por favor ingresa tu correo y tu contraseña.';
    } else {
        try {
            $sql = "SELECT id, email, clave 
                    FROM usuarios 
                    WHERE email = :email 
                    LIMIT 1";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $fila = $stmt->fetch();

            if ($fila) {
                // --- Si la contraseña está en TEXTO PLANO en la BD ---
                if ($clave === $fila['clave']) {

                    $_SESSION['usuario_id']    = $fila['id'];
                    $_SESSION['usuario_email'] = $fila['email'];

                    header('Location: panel.php');
                    exit;
                } else {
                    $mensajeError = 'Correo o contraseña incorrectos.';
                }

            } else {
                $mensajeError = 'Correo o contraseña incorrectos.';
            }
        } catch (PDOException $e) {
            $mensajeError = 'Error al intentar iniciar sesión.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="./CSS/login.css">
</head>
<body>
<div class="container">
    <div class="card">
        <h2>Iniciar sesión</h2>

        <?php if ($mensajeError !== ''): ?>
            <div class="error-box">
                <?php echo htmlspecialchars($mensajeError); ?>
            </div>
        <?php endif; ?>

        <form action="" method="post" novalidate>
            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?php echo htmlspecialchars($email); ?>"
                    required
                    placeholder="correo@ejemplo.com"
                >
            </div>

            <div class="form-group">
                <label for="clave">Contraseña</label>
                <input
                    type="password"
                    id="clave"
                    name="clave"
                    required
                    placeholder="Tu contraseña"
                >
            </div>

            <button type="submit" class="btn">Entrar</button>
        </form>

        <a href="index.php">Volver al Inicio</a>
    </div>
</div>
<script src="JS/app.js"></script>
</body>
</html>
