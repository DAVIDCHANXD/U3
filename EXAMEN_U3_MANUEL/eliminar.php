<?php
session_start();
require_once 'conexion.php';

// 1) Verificar sesión
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$mensajeOk    = '';
$mensajeError = '';

// 2) Tomar el ID por GET
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: panel.php');
    exit();
}

// 3) Obtener datos del medicamento para mostrar en la confirmación
$medicamento = null;

try {
    $sql = "SELECT m.*, p.nombre AS proveedor_nombre
            FROM medicamentos m
            LEFT JOIN proveedores p ON m.proveedor_id = p.id
            WHERE m.id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $medicamento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$medicamento) {
        $mensajeError = 'Medicamento no encontrado.';
    }
} catch (Exception $e) {
    $mensajeError = 'Error al obtener el medicamento.';
}

// 4) Si el usuario confirma (POST), eliminar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $medicamento) {
    try {
        $sqlDelete = "DELETE FROM medicamentos WHERE id = :id";
        $stmtDel   = $pdo->prepare($sqlDelete);
        $stmtDel->execute([':id' => $id]);

        $mensajeOk    = 'Medicamento eliminado correctamente.';
        $medicamento  = null; // ya no existe, ocultamos el formulario
    } catch (Exception $e) {
        $mensajeError = 'Ocurrió un error al eliminar el medicamento.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar medicamento</title>
    <!-- Puedes reutilizar el mismo CSS de editar para mantener el estilo sencillo -->
    <link rel="stylesheet" href="CSS/editar.css">
</head>
<body>
<div class="container">
    <h1>Eliminar medicamento</h1>

    <?php if ($mensajeOk !== ''): ?>
        <div class="success-box">
            <?php echo htmlspecialchars($mensajeOk); ?>
        </div>
    <?php endif; ?>

    <?php if ($mensajeError !== ''): ?>
        <div class="error-box">
            <?php echo htmlspecialchars($mensajeError); ?>
        </div>
    <?php endif; ?>

    <?php if ($medicamento): ?>
        <p><strong>¿Seguro que deseas eliminar este medicamento?</strong></p>

        <div class="form-group">
            <label>Nombre:</label>
            <div><?php echo htmlspecialchars($medicamento['nombre']); ?></div>
        </div>

        <div class="form-group">
            <label>Categoría:</label>
            <div><?php echo htmlspecialchars($medicamento['categoria']); ?></div>
        </div>

        <div class="form-group">
            <label>Cantidad:</label>
            <div><?php echo htmlspecialchars($medicamento['cantidad']); ?></div>
        </div>

        <div class="form-group">
            <label>Precio:</label>
            <div>$<?php echo htmlspecialchars($medicamento['precio']); ?></div>
        </div>

        <div class="form-group">
            <label>Proveedor:</label>
            <div><?php echo htmlspecialchars($medicamento['proveedor_nombre'] ?? 'Sin proveedor'); ?></div>
        </div>

        <form action="" method="post">
            <button type="submit" class="btn">Sí, eliminar</button>
        </form>
    <?php endif; ?>

    <a href="panel.php" class="link-back">← Volver al panel</a>
</div>
</body>
</html>
