<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard RRHH</title>
    <link rel="stylesheet" href="<?= asset('estilos.css') ?>">
    <link rel="stylesheet" href="<?= asset('dashboard.css') ?>">
    <script src="<?= asset('theme.js') ?>" defer></script>
</head>
<body class="dashboard-body">
<button class="theme-toggle" type="button" data-theme-toggle aria-label="Cambiar tema"></button>
<?php
$view = $_GET['view'] ?? 'table';
$activeView = in_array($view, ['table', 'gallery'], true) ? $view : 'table';
?>
<div class="dashboard-layout">
    <aside class="dashboard-sidebar">
        <h3>Dashboard</h3>
        <p>Panel de Administración</p>

        <span class="sidebar-section">PANEL</span>
        <a class="sidebar-link active" href="<?= route('dashboard', 'index') ?>">Inicio</a>
        <a class="sidebar-link" href="<?= route('dashboard', 'audit') ?>">Auditoría</a>

        <span class="sidebar-section">GENERAL</span>
        <a class="sidebar-link" href="<?= route('dashboard', 'addEmployee') ?>">Agregar empleado</a>
        <a class="sidebar-link" href="<?= route('dashboard', 'logout') ?>">Cerrar sesión</a>
    </aside>

    <main class="dashboard-main">
        <section class="panel panel--wide hr-panel hr-panel--fullscreen">
            <h1>Formulario Tabla Detallada</h1>

            <div class="module-headline">
                <h2>Gestión Interna (RRHH & Ops)</h2>
                <p>Recursos humanos, soporte al cliente y comunidad</p>
            </div>

            <nav class="tab-row">
                <a class="tab-item active" href="<?= route('dashboard', 'index') ?>">Recursos Humanos</a>
                <a class="tab-item" href="#">Soporte</a>
                <a class="tab-item" href="#">Comunidad</a>
            </nav>

            <div class="subtab-row">
                <a class="subtab-item active" href="<?= route('dashboard', 'index') ?>">Personal</a>
                <a class="subtab-item" href="<?= route('dashboard', 'index') ?>">Desempeño</a>
                <a class="subtab-item" href="<?= route('dashboard', 'index') ?>">Objetivos</a>
                <a class="subtab-item" href="<?= route('dashboard', 'audit') ?>">Auditoría</a>
            </div>

            <div class="module-header">
                <div>
                    <h3>Recursos Humanos</h3>
                    <p class="subtitle">Gestión de personal y empleados</p>
                </div>

                <div class="module-actions">
                    <a class="btn-secondary btn-inline" href="<?= route('dashboard', 'audit') ?>">Exportar</a>
                    <a class="btn-primary btn-inline" href="<?= route('dashboard', 'addEmployee') ?>">Agregar Empleado</a>
                </div>
            </div>
            <?php
            // calcular estadísticas por tipo de empleado
            $rowsForStats = $allUsers ?? (isset($user) ? [$user] : []);
            $totalPersonal = count($rowsForStats);
            $counts = [];
            foreach ($rowsForStats as $r) {
                $t = trim($r['type'] ?? '');
                if ($t === '') $t = 'Otro';
                if (!isset($counts[$t])) $counts[$t] = 0;
                $counts[$t]++;
            }

            $instructores = $counts['Instructor'] ?? 0;
            $desarrolladores = $counts['Desarrollador'] ?? 0;
            $administradores = $counts['Administrador'] ?? 0;
            $asistAdministrativos = $counts['Asistente Administrativo'] ?? 0;
            ?>

            <div class="stats-grid">
                <article class="stat-card">
                    <h4>Total Personal</h4>
                    <p class="stat-value"><?= $totalPersonal ?></p>
                    <small>Empleados registrados</small>
                </article>
                <article class="stat-card">
                    <h4>Instructores</h4>
                    <p class="stat-value"><?= $instructores ?></p>
                    <small>Equipo docente</small>
                </article>
                <article class="stat-card">
                    <h4>Desarrolladores</h4>
                    <p class="stat-value"><?= $desarrolladores ?></p>
                    <small>Equipo técnico</small>
                </article>
                <article class="stat-card">
                    <h4>Administradores</h4>
                    <p class="stat-value"><?= $administradores ?></p>
                    <small>Personal administrativo</small>
                </article>
                <article class="stat-card">
                    <h4>Asist. Administrativos</h4>
                    <p class="stat-value"><?= $asistAdministrativos ?></p>
                    <small>Personal de soporte</small>
                </article>
            </div>

            <div class="subtab-row media-tabs">
                <a class="subtab-item <?= $activeView === 'gallery' ? 'active' : '' ?>" href="<?= route('dashboard', 'index') ?>&view=gallery">Galería de Fotos</a>
                <a class="subtab-item <?= $activeView === 'table' ? 'active' : '' ?>" href="<?= route('dashboard', 'index') ?>&view=table">Tabla Detallada</a>
            </div>

            <form class="filter-row" method="get" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" aria-label="Filtros de búsqueda">
                <input type="hidden" name="controller" value="dashboard">
                <input type="hidden" name="action" value="index">
                <input
                    type="search"
                    name="search"
                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                    placeholder="Buscar por nombre, email o puesto..."
                    aria-label="Buscar personal">

                <select name="type" aria-label="Filtrar por tipo" onchange="this.form.submit()">
                    <option value="" <?= (isset($_GET['type']) && $_GET['type'] === '') || !isset($_GET['type']) ? 'selected' : '' ?>>Todos los tipos</option>
                    <option value="Instructor" <?= (isset($_GET['type']) && $_GET['type'] === 'Instructor') ? 'selected' : '' ?>>Instructor</option>
                    <option value="Desarrollador" <?= (isset($_GET['type']) && $_GET['type'] === 'Desarrollador') ? 'selected' : '' ?>>Desarrollador</option>
                    <option value="Administrador" <?= (isset($_GET['type']) && $_GET['type'] === 'Administrador') ? 'selected' : '' ?>>Administrador</option>
                    <option value="Asistente Administrativo" <?= (isset($_GET['type']) && $_GET['type'] === 'Asistente Administrativo') ? 'selected' : '' ?>>Asistente Administrativo</option>
                </select>

                <select name="status" aria-label="Filtrar por estado" onchange="this.form.submit()">
                    <option value="" <?= (isset($_GET['status']) && $_GET['status'] === '') || !isset($_GET['status']) ? 'selected' : '' ?>>Todos los estados</option>
                    <option value="Activo" <?= (isset($_GET['status']) && $_GET['status'] === 'Activo') ? 'selected' : '' ?>>Activo</option>
                    <option value="Inactivo" <?= (isset($_GET['status']) && $_GET['status'] === 'Inactivo') ? 'selected' : '' ?>>Inactivo</option>
                </select>

                <button class="btn-secondary" type="submit">Aplicar</button>
            </form>

            <?php if ($activeView === 'gallery'): ?>
                <div class="gallery-grid">
                    <?php
                    $galleryRows = $allUsers ?? [];
                    if (empty($galleryRows)) {
                        $galleryRows = isset($user) ? [$user] : [];
                    }
                    ?>
                    <?php foreach ($galleryRows as $u): ?>
                        <?php
                        $name = trim($u['name'] ?? '');
                        $initial = strtoupper(substr($name !== '' ? $name : 'N', 0, 1));
                        $photoUrl = trim($u['photo_url'] ?? '');
                        ?>
                        <article class="profile-card">
                            <?php if ($photoUrl !== ''): ?>
                                <div class="avatar avatar--image">
                                    <img src="<?= asset($photoUrl) ?>" alt="Foto de <?= htmlspecialchars($name) ?>">
                                </div>
                            <?php else: ?>
                                <div class="avatar"><?= htmlspecialchars($initial) ?></div>
                            <?php endif; ?>
                            <h4><?= htmlspecialchars($name) ?></h4>
                            <span class="role-chip"><?= htmlspecialchars($u['type'] ?? 'Sin tipo') ?></span>
                            <p><?= htmlspecialchars($u['email'] ?? '') ?></p>
                            <small>Registrado: <?= htmlspecialchars($u['created_at'] ?? '-') ?></small>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="table-wrap table-wrap--dashboard">
                    <table class="audit-table dashboard-table">
                        <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Tipo</th>
                            <th>Puesto</th>
                            <th>Departamento</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $rows = $allUsers ?? [];
                        if (empty($rows)) :
                            // fallback a mostrar usuario actual
                            $rows = isset($user) ? [$user] : [];
                        endif;

                        foreach ($rows as $u) :
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($u['name'] ?? '') ?></td>
                                <td><?= htmlspecialchars($u['email'] ?? '') ?></td>
                                <td><span class="role-chip"><?= htmlspecialchars($u['type'] ?? '') ?></span></td>
                                <td><?= htmlspecialchars($u['position'] ?? '') ?></td>
                                <td><?= htmlspecialchars($u['department'] ?? '') ?></td>
                                <td><span class="status-chip"><?= htmlspecialchars($u['status'] ?? '') ?></span></td>
                                <td>
                                    <a class="table-link" href="<?= route('dashboard', 'viewEmployee') ?>&id=<?= urlencode($u['id'] ?? '') ?>">Ver</a>
                                    <a class="table-link" href="<?= route('dashboard', 'editEmployee') ?>&id=<?= urlencode($u['id'] ?? '') ?>">Editar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>
</body>
</html>
