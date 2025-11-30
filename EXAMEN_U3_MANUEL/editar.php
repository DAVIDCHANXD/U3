<?php
    session_start();
    require_once 'conexion.php';

    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit();
    }

    $mensajeOk = '';
    $mensajeError = '';

    $id = $_GET['id'] ?? 0;

    if ($id == 0) {
        header('Location: panel.php');
        exit();
    }

    try {
        $sql = "SELECT m.*, p.nombre as proveedor_nombre
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
        $mensajeError = 'Error al obtener el medicamento.' . $e->getMessage();
    }

    $proveedores = [];
    try {
        $sqlProveedores = "SELECT id, nombre FROM proveedores ORDER BY nombre ASC";
        $stmtProveedores = $pdo->query($sqlProveedores);
        $proveedores = $stmtProveedores->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $mensajeError = 'Error al obtener los proveedores.' . $e->getMessage();
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre       = trim($_POST['nombre'] ?? '');
        $categoria    = trim($_POST['categoria'] ?? '');
        $cantidad     = trim($_POST['cantidad'] ?? '');
        $precio       = trim($_POST['precio'] ?? '');
        $proveedor_id = trim($_POST['proveedor_id'] ?? '');

        if (empty($nombre) || empty($categoria) || empty($cantidad) || empty($precio) || empty($proveedor_id)) {
            $mensajeError = 'Por favor llena todos los campos.';
        } elseif (!is_numeric($cantidad) || $cantidad < 0 ||  !is_numeric($precio) || $precio < 0 ) {
            $mensajeError = 'Cantidad, precio y proveedor deben ser valores numéricos positivos.';
        } else {
            try {
                $sqlUpdate = "UPDATE medicamentos
                            SET nombre = :nombre,
                                categoria = :categoria,
                                cantidad = :cantidad,
                                precio = :precio,
                                proveedor_id = :proveedor_id
                            WHERE id = :id";
                $stmt = $pdo->prepare($sqlUpdate);
                $stmt->execute([
                    ':nombre'       => $nombre,
                    ':categoria'    => $categoria,
                    ':cantidad'     => (int)$cantidad,
                    ':precio'       => (float)$precio,
                    ':proveedor_id' => (int)$proveedor_id,
                    ':id'           => $id,
                ]);

                $mensajeOk = 'Medicamento actualizado correctamente.';

                $sql = "SELECT m.*, p.nombre as proveedor_nombre
                        FROM medicamentos m
                        LEFT JOIN proveedores p ON m.proveedor_id = p.id
                        WHERE m.id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':id' => $id]);
                $medicamento = $stmt->fetch(PDO::FETCH_ASSOC);
                
            } catch (Exception $e) {
                $mensajeError = 'Ocurrió un error al actualizar el medicamento.' . $e->getMessage();
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar medicamento</title>
    <link rel="stylesheet" href="CSS/editar.css">
</head>
<body>
<div class="container">
    <h1>Editar medicamento</h1>

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

    <?php if (isset($medicamento) && $medicamento): ?>
    <form action="" method="post">
        <div class="form-group">
            <label for="nombre">Nombre del medicamento</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($medicamento['nombre']); ?>" required>
        </div>

        <div class="form-group">
            <label for="categoria">Categoría</label>
            <input type="text" id="categoria" name="categoria" value="<?php echo htmlspecialchars($medicamento['categoria']); ?>" required>
        </div>

        <div class="form-group">
            <label for="cantidad">Cantidad</label>
            <input type="number" id="cantidad" name="cantidad" value="<?php echo htmlspecialchars($medicamento['cantidad']); ?>" min="0" required>
        </div>

        <div>
            <label for="precio">Precio</label>
            <input type="number" id="precio" name="precio" value="<?php echo htmlspecialchars($medicamento['precio']); ?>" min="0" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="proveedor_id">Provedor</label>
            <select name="proveedor_id" id="proveedor_id" required>
                <option value="">Selecciona un proveedor</option>
                <?php foreach ($proveedores as $proveedor): ?>
                    <option value="<?php echo $proveedor['id']; ?>"
                        <?php echo ($medicamento['proveedor_id'] == $proveedor['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($proveedor['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn">Actualizar medicamento</button>
    </form>
    <?php endif; ?>
    <a href="panel.php" class="link-back">Volver al panel</a>
    </div>

<script src="JS/app.js"></script>
</body>
</html>