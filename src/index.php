<?php
require_once 'db.php';

$mensaje = "";
$error_crud = "";

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        header("Location: index.php?msg=deleted");
        exit;
    } catch (PDOException $e) {
        $error_crud = "Error al eliminar: " . $e->getMessage();
    }
}

// 2. CREAR O EDITAR PRODUCTO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = floatval($_POST['precio'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);

    if (!empty($nombre) && $precio >= 0) {
        try {
            if ($id) {
                // Actualizar
                $stmt = $pdo->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ? WHERE id = ?");
                $stmt->execute([$nombre, $descripcion, $precio, $stock, $id]);
                header("Location: index.php?msg=updated");
                exit;
            } else {
                // Insertar
                $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, stock) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nombre, $descripcion, $precio, $stock]);
                header("Location: index.php?msg=created");
                exit;
            }
        } catch (PDOException $e) {
            $error_crud = "Error en la base de datos: " . $e->getMessage();
        }
    } else {
        $error_crud = "Por favor completa los campos obligatorios.";
    }
}

// 3. OBTENER PRODUCTO A EDITAR (Si existe ID en URL)
$producto_editar = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $producto_editar = $stmt->fetch(PDO::FETCH_ASSOC);
}

// 4. LEER TODOS LOS PRODUCTOS
try {
    $stmt = $pdo->query("SELECT * FROM productos ORDER BY id DESC");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_crud = "Error al consultar productos: " . $e->getMessage();
    $productos = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - CRUD</title>
    <!-- CSS de Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📦 Gestión de Productos</h2>
        <a href="index.php" class="btn btn-primary">+ Nuevo Producto</a>
    </div>

    <!-- Mensajes de Estado -->
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
                if ($_GET['msg'] === 'created') echo "Producto agregado correctamente.";
                if ($_GET['msg'] === 'updated') echo "Producto actualizado correctamente.";
                if ($_GET['msg'] === 'deleted') echo "Producto eliminado correctamente.";
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($error_crud): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error_crud); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Formulario de Registro / Edición -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white font-semibold">
                    <?php echo $producto_editar ? '✏️ Editar Producto' : '➕ Agregar Producto'; ?>
                </div>
                <div class="card-body">
                    <form action="index.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $producto_editar['id'] ?? ''; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Nombre del Producto *</label>
                            <input type="text" name="nombre" class="form-control" required 
                                   value="<?php echo htmlspecialchars($producto_editar['nombre'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="3"><?php echo htmlspecialchars($producto_editar['descripcion'] ?? ''); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Precio ($) *</label>
                            <input type="number" step="0.01" name="precio" class="form-control" required 
                                   value="<?php echo htmlspecialchars($producto_editar['precio'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Stock *</label>
                            <input type="number" name="stock" class="form-control" required 
                                   value="<?php echo htmlspecialchars($producto_editar['stock'] ?? '0'); ?>">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <?php echo $producto_editar ? 'Guardar Cambios' : 'Crear Producto'; ?>
                            </button>
                            <?php if ($producto_editar): ?>
                                <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabla de Productos -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($productos)): ?>
                                    <?php foreach ($productos as $p): ?>
                                        <tr>
                                            <td><strong>#<?php echo $p['id']; ?></strong></td>
                                            <td>
                                                <div><strong><?php echo htmlspecialchars($p['nombre']); ?></strong></div>
                                                <small class="text-muted"><?php echo htmlspecialchars($p['descripcion'] ?? 'Sin descripción'); ?></small>
                                            </td>
                                            <td>$<?php echo number_format($p['precio'], 2); ?></td>
                                            <td>
                                                <span class="badge <?php echo $p['stock'] > 0 ? 'bg-info' : 'bg-danger'; ?>">
                                                    <?php echo $p['stock']; ?> und.
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="index.php?action=edit&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-warning me-1">
                                                    ✏️ Editar
                                                </a>
                                                <a href="index.php?action=delete&id=<?php echo $p['id']; ?>" 
                                                   class="btn btn-sm btn-outline-danger" 
                                                   onclick="return confirm('¿Seguro que deseas eliminar este producto?');">
                                                    🗑️ Eliminar
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No hay productos disponibles.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
