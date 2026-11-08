<?php

class SolicitudData
{
    public string $estudianteId;
    public string $tipo;
    public string $titulo;
    public string $descripcion;
    public string $prioridad;

    public function __construct(
        string $estudianteId,
        string $tipo,
        string $titulo,
        string $descripcion,
        string $prioridad
    ) {
        $this->estudianteId = $estudianteId;
        $this->tipo = $tipo;
        $this->titulo = $titulo;
        $this->descripcion = $descripcion;
        $this->prioridad = $prioridad;
    }
}