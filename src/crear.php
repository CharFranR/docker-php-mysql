<?php
require_once __DIR__ . '/db.php';

$error_crud = "";

$nombre = '';
$descripcion = '';
$precio = '';
$stock = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = floatval($_POST['precio'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);

    if ($nombre !== '' && $precio >= 0) {
        try {
            $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, stock) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nombre, $descripcion, $precio, $stock]);
            header("Location: index.php?msg=created");
            exit;
        } catch (PDOException $e) {
            $error_crud = "Error en la base de datos: " . $e->getMessage();
        }
    } else {
        $error_crud = "Por favor completa los campos obligatorios.";
    }
}

$page_title = 'Nuevo Producto - CRUD';
require __DIR__ . '/partials/header.php';
?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>➕ Nuevo Producto</h2>
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
                <div class="card-header bg-white font-semibold">➕ Agregar Producto</div>
                <div class="card-body">
                    <form action="crear.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nombre del Producto *</label>
                            <input type="text" name="nombre" class="form-control" required
                                   value="<?php echo htmlspecialchars($nombre); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="3"><?php echo htmlspecialchars($descripcion); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Precio ($) *</label>
                            <input type="number" step="0.01" name="precio" class="form-control" required
                                   value="<?php echo htmlspecialchars((string)$precio); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Stock *</label>
                            <input type="number" name="stock" class="form-control" required
                                   value="<?php echo htmlspecialchars((string)$stock); ?>">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">Crear Producto</button>
                            <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php require __DIR__ . '/partials/footer.php'; ?>
