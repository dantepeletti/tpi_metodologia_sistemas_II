<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

require_once dirname(__DIR__) . '/src/Repositories/SolicitudRepository.php';
require_once dirname(__DIR__) . '/src/Controllers/SolicitudController.php';
require_once dirname(__DIR__) . '/src/Models/SolicitudData.php';
require_once dirname(__DIR__) . '/config/SolicitudConfig.php';

$conexion = require dirname(__DIR__) . '/config/database.php';
if ($conexion['database'] === null) {
    $mensajeError = 'No se pudo conectar con la base de datos. Intentá nuevamente más tarde.';
}

$solicitudes = [];
$estudiantesDisponibles = [];
$mensajeError = null;


if ($conexion['database'] !== null) {

    $collection = $conexion['database']->selectCollection('solicitudes');

    $estudiantes = $conexion['database']->selectCollection('estudiantes');

    $responsables = $conexion['database']->selectCollection('responsables');

        $repository = new SolicitudRepository(
            $collection,
            $estudiantes,
            $responsables
        );

    $controller = new SolicitudController($repository);

    $solicitudes = $controller->index();

    $estudiantesDisponibles = $repository->obtenerEstudiantesActivos();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar'])) {

    $datos = [
        'estudianteId' => $_POST['estudianteId'] ?? '',
        'tipo' => $_POST['tipo'] ?? '',
        'titulo' => trim($_POST['titulo'] ?? ''),
        'descripcion' => trim($_POST['descripcion'] ?? ''),
        'prioridad' => $_POST['prioridad'] ?? ''
    ];

if (!$controller->validarDatos($datos)) {
    $mensajeError = 'Verificá los datos ingresados.';
} else {
        $solicitud = new SolicitudData(
        $datos['estudianteId'],
        $datos['tipo'],
        $datos['titulo'],
        $datos['descripcion'],
        $datos['prioridad']
    );

    $controller->crear($solicitud);

    header('Location: index.php');
    exit;
}
}

}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Solicitudes Académicas</title>
    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<main class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h1 class="mb-0">Gestión de Solicitudes Académicas</h1>

        <button type="button" id="btnTema" class="btn btn-outline-secondary">
            <span id="iconoTema">🌙</span>
            <span id="textoTema">Modo oscuro</span>
        </button>
    </div>

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-white">
            <h2 class="h5 mb-0">Registrar nueva solicitud</h2>
        </div>

        <div class="card-body">

            <?php if ($mensajeError !== null): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($mensajeError) ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <input type="hidden" name="registrar" value="1">

                <div class="mb-3">
                    <label class="form-label">Estudiante</label>

                    <select name="estudianteId" class="form-select" required>
                        <option value="">Seleccionar estudiante</option>

                        <?php foreach ($estudiantesDisponibles as $estudiante): ?>
                            <option value="<?= $estudiante['_id'] ?>">
                                <?= htmlspecialchars(
                                    $estudiante['nombre'] . ' ' . $estudiante['apellido']
                                ) ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipo de solicitud</label>

                    <select name="tipo" class="form-select" required>
                        <option value="">Seleccionar tipo</option>
                        <option value="certificado">Certificado</option>
                        <option value="consulta">Consulta</option>
                        <option value="inscripcion">Inscripción</option>
                        <option value="documentacion">Documentación</option>
                        <option value="equivalencia">Equivalencia</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Título</label>
                    <input type="text" name="titulo" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Prioridad</label>

                    <select name="prioridad" class="form-select" required>
                        <option value="">Seleccionar prioridad</option>
                        <option value="baja">Baja</option>
                        <option value="media">Media</option>
                        <option value="alta">Alta</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    Registrar solicitud
                </button>

            </form>

        </div>
    </div>

    <div class="card shadow-sm">

        <div class="card-header bg-white">
            <h2 class="h5 mb-0">Solicitudes registradas</h2>
        </div>

        <div class="card-body">

            <?php if (empty($solicitudes)): ?>

                <div class="alert alert-info">
                    No hay solicitudes registradas.
                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table table-hover table-striped align-middle">

                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Estudiante</th>
                                <th>Responsable</th>
                                <th>Tipo</th>
                                <th>Prioridad</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($solicitudes as $item): ?>

                                <?php
                                    $solicitud = $item['solicitud'];
                                    $estudiante = $item['estudiante'];
                                    $responsable = $item['responsable'];
                                ?>

                                <tr>
                                    <td>
                                        <?= htmlspecialchars($solicitud['titulo']) ?>
                                    </td>

                                    <td>
                                        <?= $estudiante
                                            ? htmlspecialchars($estudiante['nombre'] . ' ' . $estudiante['apellido'])
                                            : 'Sin estudiante' ?>
                                    </td>

                                    <td>
                                        <?= $responsable
                                            ? htmlspecialchars($responsable['nombre'])
                                            : 'Sin asignar' ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($solicitud['tipo']) ?>
                                    </td>

                                    <td>
                                        <?php
                                            $clasePrioridad = match ($solicitud['prioridad']) {
                                                'alta' => 'prioridad-alta',
                                                'media' => 'prioridad-media',
                                                'baja' => 'prioridad-baja',
                                                default => 'prioridad-default'
                                            };
                                        ?>

                                        <span class="badge <?= $clasePrioridad ?>">
                                            <?= htmlspecialchars(ucfirst($solicitud['prioridad'])) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?php
                                            $claseEstado = match ($solicitud['estado']) {
                                                'pendiente' => 'secondary',
                                                'en_proceso' => 'primary',
                                                'resuelta' => 'success',
                                                default => 'dark'
                                            };

                                            $nombreEstado = match ($solicitud['estado']) {
                                                'en_proceso' => 'En proceso',
                                                default => ucfirst($solicitud['estado'])
                                            };
                                        ?>

                                        <span class="badge text-bg-<?= $claseEstado ?>">
                                            <?= htmlspecialchars($nombreEstado) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= $solicitud['fechaCreacion']->toDateTime()->format('d/m/Y') ?>
                                    </td>
                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </div>

    </div>

</main>

    <!-- Bootstrap JS -->
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

    <script src="assets/js/app.js"></script>
    <script>
        const botonTema = document.getElementById('btnTema');
        const iconoTema = document.getElementById('iconoTema');
        const textoTema = document.getElementById('textoTema');

        const temaGuardado = localStorage.getItem('tema') || 'light';

        document.documentElement.setAttribute('data-bs-theme', temaGuardado);

        actualizarBoton();

        botonTema.addEventListener('click', () => {
            const temaActual = document.documentElement.getAttribute('data-bs-theme');

            const nuevoTema = temaActual === 'dark' ? 'light' : 'dark';

            document.documentElement.setAttribute('data-bs-theme', nuevoTema);

            localStorage.setItem('tema', nuevoTema);

            actualizarBoton();
        });

        function actualizarBoton() {
            const temaActual = document.documentElement.getAttribute('data-bs-theme');

            iconoTema.textContent = temaActual === 'dark' ? '☀️' : '🌙';
            textoTema.textContent = temaActual === 'dark' ? 'Modo claro' : 'Modo oscuro';
        }
    </script>
</body>
</html>