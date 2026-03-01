<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Empleado</title>
    <link rel="stylesheet" href="<?= asset('estilos.css') ?>">
    <link rel="stylesheet" href="<?= asset('dashboard.css') ?>">
    <script src="<?= asset('theme.js') ?>" defer></script>
    <style>
        .employee-detail-card {
            background: var(--bg-secondary, #fff);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .employee-header {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid var(--border-color, #e0e0e0);
        }

        .employee-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: bold;
            color: white;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .employee-header-info h2 {
            margin: 0;
            font-size: 1.8rem;
            color: var(--text-primary, #000);
            font-weight: 700;
        }

        .employee-header-info p {
            margin: 0.5rem 0 0 0;
            color: var(--text-secondary, #666);
            font-size: 1rem;
            font-weight: 600;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-item label {
            font-weight: 700;
            color: var(--text-secondary, #999);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .detail-item .value {
            font-size: 1.05rem;
            color: var(--text-primary, #000);
            padding: 0.75rem;
            background: var(--bg-tertiary, #f5f5f5);
            border-radius: 6px;
            border-left: 4px solid #667eea;
            word-break: break-word;
        }

        .status-badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .status-badge--active {
            background: #d4edda;
            color: #155724;
        }

        .status-badge--inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--primary-color, #667eea);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.3s;
            margin-top: 2rem;
        }

        .btn-back:hover {
            background: var(--primary-dark, #5568d3);
        }

        @media (max-width: 768px) {
            .employee-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .employee-avatar {
                width: 80px;
                height: 80px;
                font-size: 2rem;
            }

            .employee-header-info h2 {
                font-size: 1.5rem;
            }

            .detail-grid {
                gap: 1rem;
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="dashboard-body">
<button class="theme-toggle" type="button" data-theme-toggle aria-label="Cambiar tema"></button>

<div class="dashboard-layout">
    <aside class="dashboard-sidebar">
        <h3>Dashboard</h3>
        <p>Panel de Administración</p>

        <span class="sidebar-section">PANEL</span>
        <a class="sidebar-link" href="<?= route('dashboard', 'index') ?>">Inicio</a>
        <a class="sidebar-link" href="<?= route('dashboard', 'audit') ?>">Auditoría</a>

        <span class="sidebar-section">GENERAL</span>
        <a class="sidebar-link" href="<?= route('dashboard', 'addEmployee') ?>">Agregar empleado</a>
        <a class="sidebar-link" href="<?= route('dashboard', 'logout') ?>">Cerrar sesión</a>
    </aside>

    <main class="dashboard-main">
        <section class="panel panel--wide hr-panel hr-panel--fullscreen">
            <?php if (isset($user) && $user): ?>
                <h1>Información del Empleado</h1>

                <div class="employee-detail-card">
                    <!-- Encabezado con avatar y nombre -->
                    <div class="employee-header">
                        <div class="employee-avatar"><?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?></div>
                        <div class="employee-header-info">
                            <h2><?= htmlspecialchars($user['name'] ?? 'N/A') ?></h2>
                            <p><?= htmlspecialchars($user['type'] ?? 'N/A') ?></p>
                        </div>
                    </div>

                    <!-- Información de Contacto -->
                    <h3 style="font-size: 1.1rem; color: var(--text-secondary, #666); font-weight: 700; margin-top: 2rem; margin-bottom: 1rem;">Información de Contacto</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Email</label>
                            <div class="value"><?= htmlspecialchars($user['email'] ?? 'N/A') ?></div>
                        </div>
                        <div class="detail-item">
                            <label>Teléfono</label>
                            <div class="value"><?= htmlspecialchars($user['phone'] ?? 'No registrado') ?></div>
                        </div>
                    </div>

                    <!-- Información Laboral -->
                    <h3 style="font-size: 1.1rem; color: var(--text-secondary, #666); font-weight: 700; margin-top: 2rem; margin-bottom: 1rem;">Información Laboral</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Departamento</label>
                            <div class="value"><?= htmlspecialchars($user['department'] ?? 'No asignado') ?></div>
                        </div>
                        <div class="detail-item">
                            <label>Puesto</label>
                            <div class="value"><?= htmlspecialchars($user['position'] ?? 'No asignado') ?></div>
                        </div>
                        <div class="detail-item">
                            <label>Fecha de Contratación</label>
                            <div class="value"><?= htmlspecialchars($user['hired_at'] ?? 'No registrada') ?></div>
                        </div>
                    </div>

                    <!-- Estado -->
                    <h3 style="font-size: 1.1rem; color: var(--text-secondary, #666); font-weight: 700; margin-top: 2rem; margin-bottom: 1rem;">Estado</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Estado Actual</label>
                            <div class="value">
                                <span class="status-badge <?= strtolower($user['status'] ?? '') === 'activo' ? 'status-badge--active' : 'status-badge--inactive' ?>">
                                    <?= htmlspecialchars($user['status'] ?? 'N/A') ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Metadata -->
                    <h3 style="font-size: 1.1rem; color: var(--text-secondary, #666); font-weight: 700; margin-top: 2rem; margin-bottom: 1rem;">Información Adicional</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Registrado en</label>
                            <div class="value"><?= htmlspecialchars($user['created_at'] ?? 'N/A') ?></div>
                        </div>
                        <div class="detail-item">
                            <label>ID del Empleado</label>
                            <div class="value" style="font-family: monospace; font-size: 0.9rem;"><?= htmlspecialchars($user['id'] ?? 'N/A') ?></div>
                        </div>
                    </div>

                    <a href="<?= route('dashboard', 'index') ?>" class="btn-back">← Volver al Dashboard</a>
                </div>
            <?php else: ?>
                <h1>Empleado no encontrado</h1>
                <div class="employee-detail-card">
                    <p>No se pudo encontrar el empleado solicitado.</p>
                    <a href="<?= route('dashboard', 'index') ?>" class="btn-back">← Volver al Dashboard</a>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>

</body>
</html>