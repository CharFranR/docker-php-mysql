<?php
require_once __DIR__ . '/db.php';

$error_crud = "";

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if (isset($_GET['error']) && $_GET['error'] === 'delete') {
    $error_crud = "Error al eliminar el producto.";
}

try {
    if ($q !== '') {
        $stmt = $pdo->prepare("SELECT * FROM productos WHERE nombre LIKE ? ORDER BY id DESC");
        $stmt->execute(['%' . $q . '%']);
    } else {
        $stmt = $pdo->query("SELECT * FROM productos ORDER BY id DESC");
    }
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_crud = "Error al consultar productos: " . $e->getMessage();
    $productos = [];
}

$page_title = 'Gestión de Productos - CRUD';
require __DIR__ . '/partials/header.php';
?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📦 Gestión de Productos</h2>
        <a href="crear.php" class="btn btn-primary">+ Nuevo Producto</a>
    </div>

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

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="index.php" method="GET" class="row g-2 align-items-center">
                <div class="col">
                    <input type="text" name="q" class="form-control"
                           placeholder="Buscar por nombre..."
                           value="<?php echo htmlspecialchars($q); ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                    <?php if ($q !== ''): ?>
                        <a href="index.php" class="btn btn-outline-secondary">Limpiar</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

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
                                        <a href="editar.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-warning me-1">
                                            ✏️ Editar
                                        </a>
                                        <form action="eliminar.php" method="POST" class="d-inline"
                                              onsubmit="return confirm('¿Seguro que deseas eliminar este producto?');">
                                            <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                🗑️ Eliminar
                                            </button>
                                        </form>
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
<?php require __DIR__ . '/partials/footer.php'; ?>
