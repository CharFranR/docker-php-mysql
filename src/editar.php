<?php
require_once __DIR__ . '/db.php';

$error_crud = "";

$id = isset($_POST['id']) ? intval($_POST['id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);

$stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->execute([$id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = floatval($_POST['precio'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);

    if ($nombre !== '' && $precio >= 0) {
        try {
            $stmt = $pdo->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ? WHERE id = ?");
            $stmt->execute([$nombre, $descripcion, $precio, $stock, $id]);
            header("Location: index.php?msg=updated");
            exit;
        } catch (PDOException $e) {
            $error_crud = "Error en la base de datos: " . $e->getMessage();
        }
    } else {
        $error_crud = "Por favor completa los campos obligatorios.";
    }

    $producto['nombre'] = $nombre;
    $producto['descripcion'] = $descripcion;
    $producto['precio'] = $precio;
    $producto['stock'] = $stock;
}

$page_title = 'Editar Producto - CRUD';
require __DIR__ . '/partials/header.php';
?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>✏️ Editar Producto #<?php echo $producto['id']; ?></h2>
        <a href="index.php" class="btn btn-outline-secondary">← Volver</a>
    </div>

    <?php if ($error_crud): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error_crud); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white font-semibold">✏️ Editar Producto</div>
                <div class="card-body">
                    <form action="editar.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">

                        <div class="mb-3">
                            <label class="form-label">Nombre del Producto *</label>
                            <input type="text" name="nombre" class="form-control" required
                                   value="<?php echo htmlspecialchars($producto['nombre']); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="3"><?php echo htmlspecialchars($producto['descripcion'] ?? ''); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Precio ($) *</label>
                            <input type="number" step="0.01" name="precio" class="form-control" required
                                   value="<?php echo htmlspecialchars((string)$producto['precio']); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Stock *</label>
                            <input type="number" name="stock" class="form-control" required
                                   value="<?php echo htmlspecialchars((string)$producto['stock']); ?>">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">Guardar Cambios</button>
                            <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php require __DIR__ . '/partials/footer.php'; ?>
