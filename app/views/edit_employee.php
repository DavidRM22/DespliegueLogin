<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Empleado</title>
    <link rel="stylesheet" href="<?= asset('estilos.css') ?>">
    <link rel="stylesheet" href="<?= asset('dashboard.css') ?>">
    <script src="<?= asset('theme.js') ?>" defer></script>
</head>
<body class="dashboard-body">
<button class="theme-toggle" type="button" data-theme-toggle aria-label="Cambiar tema"></button>
<div class="employee-shell">
    <section class="employee-modal">
        <div class="employee-modal__header">
            <h1>Editar Empleado</h1>
            <a class="employee-modal__close" href="<?= route('dashboard', 'index') ?>" aria-label="Cerrar">×</a>
        </div>

        <?php if (isset($user) && $user): ?>
            <form class="employee-form" method="post" action="<?= route('dashboard', 'saveEditEmployee') ?>">
                <input type="hidden" name="id" value="<?= htmlspecialchars($user['id'] ?? '') ?>">

                <h2>Información del Usuario</h2>
                <div class="employee-grid employee-grid--two">
                    <div class="field">
                        <label for="full_name">Nombre Completo *</label>
                        <input id="full_name" name="full_name" type="text" placeholder="Ej: Juan Pérez García" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                    </div>

                    <div class="field">
                        <label for="email">Correo Electrónico *</label>
                        <input id="email" name="email" type="email" placeholder="juan.perez@empresa.com" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    </div>

                    <div class="field">
                        <label for="phone">Teléfono</label>
                        <input id="phone" name="phone" type="text" placeholder="+51 999 999 999" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                    </div>
                </div>

                <h2>Información Laboral</h2>
                <div class="employee-grid employee-grid--two">
                    <div class="field">
                        <label for="type">Tipo de Empleado *</label>
                        <select id="type" name="type" required>
                            <option value="">Seleccionar tipo</option>
                            <option value="Administrador" <?= ($user['type'] ?? '') === 'Administrador' ? 'selected' : '' ?>>Administrador</option>
                            <option value="Instructor" <?= ($user['type'] ?? '') === 'Instructor' ? 'selected' : '' ?>>Instructor</option>
                            <option value="Desarrollador" <?= ($user['type'] ?? '') === 'Desarrollador' ? 'selected' : '' ?>>Desarrollador</option>
                            <option value="Asistente Administrativo" <?= ($user['type'] ?? '') === 'Asistente Administrativo' ? 'selected' : '' ?>>Asistente Administrativo</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="department">Departamento</label>
                        <select id="department" name="department">
                            <option value="">Seleccionar departamento</option>
                            <option value="Administracion" <?= ($user['department'] ?? '') === 'Administracion' ? 'selected' : '' ?>>Administración</option>
                            <option value="Recursos Humanos" <?= ($user['department'] ?? '') === 'Recursos Humanos' ? 'selected' : '' ?>>Recursos Humanos</option>
                            <option value="Tecnologia de la Informacion" <?= ($user['department'] ?? '') === 'Tecnologia de la Informacion' ? 'selected' : '' ?>>Tecnología de la Información</option>
                            <option value="Desarrollo" <?= ($user['department'] ?? '') === 'Desarrollo' ? 'selected' : '' ?>>Desarrollo</option>
                            <option value="Educacion" <?= ($user['department'] ?? '') === 'Educacion' ? 'selected' : '' ?>>Educación</option>
                            <option value="Marketing" <?= ($user['department'] ?? '') === 'Marketing' ? 'selected' : '' ?>>Marketing</option>
                            <option value="Ventas" <?= ($user['department'] ?? '') === 'Ventas' ? 'selected' : '' ?>>Ventas</option>
                            <option value="Soporte Tecnico" <?= ($user['department'] ?? '') === 'Soporte Tecnico' ? 'selected' : '' ?>>Soporte Técnico</option>
                            <option value="Operaciones" <?= ($user['department'] ?? '') === 'Operaciones' ? 'selected' : '' ?>>Operaciones</option>
                            <option value="Finanzas" <?= ($user['department'] ?? '') === 'Finanzas' ? 'selected' : '' ?>>Finanzas</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="position">Puesto / Posición</label>
                        <select id="position" name="position">
                            <option value="">Seleccionar puesto</option>
                            <option value="Instructor Senior" <?= ($user['position'] ?? '') === 'Instructor Senior' ? 'selected' : '' ?>>Instructor Senior</option>
                            <option value="Instructor Junior" <?= ($user['position'] ?? '') === 'Instructor Junior' ? 'selected' : '' ?>>Instructor Junior</option>
                            <option value="Coordinador de Contenidos" <?= ($user['position'] ?? '') === 'Coordinador de Contenidos' ? 'selected' : '' ?>>Coordinador de Contenidos</option>
                            <option value="Especialista en Formacion" <?= ($user['position'] ?? '') === 'Especialista en Formacion' ? 'selected' : '' ?>>Especialista en Formación</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="hired_at">Fecha de Contratación</label>
                        <input id="hired_at" name="hired_at" type="date" value="<?= htmlspecialchars($user['hired_at'] ?? '') ?>">
                    </div>

                    <div class="field">
                        <label for="status">Estado *</label>
                        <select id="status" name="status" required>
                            <option value="">Seleccionar estado</option>
                            <option value="Activo" <?= ($user['status'] ?? '') === 'Activo' ? 'selected' : '' ?>>Activo</option>
                            <option value="Inactivo" <?= ($user['status'] ?? '') === 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="employee-actions">
                    <a class="btn-secondary btn-inline" href="<?= route('dashboard', 'index') ?>">Cancelar</a>
                    <button class="btn-primary btn-inline" type="submit">Guardar Cambios</button>
                </div>
            </form>
        <?php else: ?>
            <div style="padding: 2rem; text-align: center;">
                <p>Empleado no encontrado</p>
                <a class="btn-primary btn-inline" href="<?= route('dashboard', 'index') ?>">Volver al Dashboard</a>
            </div>
        <?php endif; ?>
    </section>
</div>
</body>
</html>