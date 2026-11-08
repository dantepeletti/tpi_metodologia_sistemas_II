<?php

class SolicitudController
{
    private SolicitudRepository $repository;

    public function __construct(SolicitudRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(): array
    {
        return $this->repository->obtenerTodas();
    }

    public function crear(SolicitudData $datos): void
    {
        $this->repository->registrar($datos);
    }

    public function validarDatos(array $datos): bool {
    $tiposValidos = [
        'certificado',
        'consulta',
        'inscripcion',
        'documentacion',
        'equivalencia'
    ];

    $prioridadesValidas = ['baja', 'media', 'alta'];

    return (
        preg_match('/^[a-f\d]{24}$/i', $datos['estudianteId']) &&
        in_array($datos['tipo'], $tiposValidos, true) &&
        in_array($datos['prioridad'], $prioridadesValidas, true) &&
        $datos['titulo'] !== '' &&
        $datos['descripcion'] !== ''
    );
}
}