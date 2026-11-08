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
}