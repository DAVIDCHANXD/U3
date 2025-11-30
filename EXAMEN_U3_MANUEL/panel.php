<?php
// panel.php
session_start();

// Conexión PDO
require_once 'conexion.php';

// 1) Leer categoría desde GET para el filtro
$categoriaSeleccionada = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';

// 2) Obtener listado de categorías distintas (para el <select>)
$sqlCategorias = "SELECT DISTINCT categoria FROM medicamentos ORDER BY categoria";
$stmtCat = $pdo->query($sqlCategorias);
$categorias = $stmtCat->fetchAll(PDO::FETCH_COLUMN); // solo la columna "categoria"

// 3) Construir consulta principal de medicamentos
$sql = "SELECT m.id,
               m.nombre,
               m.categoria,
               m.cantidad,
               m.precio,
               p.nombre AS proveedor
        FROM medicamentos m
        INNER JOIN proveedores p ON m.proveedor_id = p.id";

$params = [];

if ($categoriaSeleccionada !== '') {
    $sql .= " WHERE m.categoria = :categoria";
    $params[':categoria'] = $categoriaSeleccionada;
}

$sql .= " ORDER BY m.nombre ASC";

$stmt = $pdo->prepare($sql); 
$stmt->execute($params);

// Traemos todo a un arreglo
$medicamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de inventario</title>
    <link rel="stylesheet" href="CSS/panel.css">
</head>
<body>
<header>
    <h1>Panel de Inventario – Salud Total</h1>
    <div>
        <span>
            Usuario:
            <?php echo isset($_SESSION['usuario_email'])
                ? htmlspecialchars($_SESSION['usuario_email'])
                : ' invitado'; ?>
        </span>
        <a href="logout.php">Cerrar sesión</a>
    </div>
</header>

<section>
    <h2>Medicamentos</h2>

    <!-- Filtro por categoría (GET) -->
    <form method="get" action="panel.php">
        <label for="categoria">Filtrar por categoría:</label>
        <select name="categoria" id="categoria">
            <option value="">-- Todas --</option>
            <?php foreach ($categorias as $cat): ?>
                <option
                    value="<?php echo htmlspecialchars($cat); ?>"
                    <?php echo ($categoriaSeleccionada === $cat) ? 'selected' : ''; ?>
                >
                    <?php echo htmlspecialchars($cat); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Filtrar</button>
        <a href="panel.php">Quitar filtro</a>
    </form>

    <p>
        <?php if ($categoriaSeleccionada !== ''): ?>
            Mostrando medicamentos de la categoría:
            <strong><?php echo htmlspecialchars($categoriaSeleccionada); ?></strong>
        <?php else: ?>
            Mostrando todos los medicamentos.
        <?php endif; ?>
    </p>

    <!-- Enlace para ir a registro de medicamentos -->
    <p>
        <a href="registro.php">➕ Registrar nuevo medicamento</a>
    </p>

    <!-- Tabla de medicamentos -->
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Proveedor</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($medicamentos) > 0): ?>
            <?php foreach ($medicamentos as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['categoria']); ?></td>
                    <td><?php echo htmlspecialchars($row['cantidad']); ?></td>
                    <td>$<?php echo number_format($row['precio'], 2); ?></td>
                    <td><?php echo htmlspecialchars($row['proveedor']); ?></td>
                    <td>
                        <a href="editar.php?id=<?php echo urlencode($row['id']); ?>">Editar</a>
                    <a href="eliminar.php?id=<?php echo urlencode($row['id']); ?>" data-confirm="¿Seguro que quieres eliminar este medicamento?"> Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">No hay medicamentos registrados.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</section>
<script src="JS/app.js"></script>
</body>
</html>
