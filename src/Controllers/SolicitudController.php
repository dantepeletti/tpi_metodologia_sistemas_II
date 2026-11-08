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

    public function validarDatos(array $datos): bool
    {
        return (
            preg_match('/^[a-f\d]{24}$/i', $datos['estudianteId']) &&
            in_array($datos['tipo'], SolicitudConfig::TIPOS_VALIDOS, true) &&
            in_array($datos['prioridad'], SolicitudConfig::PRIORIDADES_VALIDAS, true) &&
            $datos['titulo'] !== '' &&
            $datos['descripcion'] !== ''
        );
    }

    public function obtenerEstudiantesActivos(): array
    {
        return $this->repository->obtenerEstudiantesActivos();
    }
}