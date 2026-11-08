
<?php

class SolicitudRepository
{
    private MongoDB\Collection $collection;
    private MongoDB\Collection $estudiantes;
    private MongoDB\Collection $responsables;

    public function __construct(
        MongoDB\Collection $collection,
        MongoDB\Collection $estudiantes,
        MongoDB\Collection $responsables
    ) {
        $this->collection = $collection;
        $this->estudiantes = $estudiantes;
        $this->responsables = $responsables;
    }

public function obtenerEstudiantesActivos(): array
{
    return $this->estudiantes->find(
        ['activo' => true],
        ['sort' => ['apellido' => 1]]
    )->toArray();
}

public function registrar(SolicitudData $datos): void
{
    $this->collection->insertOne([
        'estudianteId' => new MongoDB\BSON\ObjectId($datos->estudianteId),
        'responsableId' => null,
        'tipo' => $datos->tipo,
        'titulo' => $datos->titulo,
        'descripcion' => $datos->descripcion,
        'prioridad' => $datos->prioridad,
        'estado' => 'pendiente',
        'fechaCreacion' => new MongoDB\BSON\UTCDateTime(),
        'fechaActualizacion' => new MongoDB\BSON\UTCDateTime()
    ]);
} 

    public function obtenerTodas(): array
    {   
    $solicitudes = $this->collection->find(
        [],
        ['sort' => ['fechaCreacion' => -1]]
    )->toArray();

    $estudianteIds = [];
    $responsableIds = [];

    foreach ($solicitudes as $solicitud) {
        $estudianteIds[(string) $solicitud['estudianteId']] =
            $solicitud['estudianteId'];

        if ($solicitud['responsableId'] !== null) {
            $responsableIds[(string) $solicitud['responsableId']] =
                $solicitud['responsableId'];
        }
    }

    $estudiantesPorId = [];
    $responsablesPorId = [];

    if (!empty($estudianteIds)) {
        $estudiantes = $this->estudiantes->find([
            '_id' => ['$in' => array_values($estudianteIds)]
        ])->toArray();

        foreach ($estudiantes as $estudiante) {
            $estudiantesPorId[(string) $estudiante['_id']] = $estudiante;
        }
    }

    if (!empty($responsableIds)) {
        $responsables = $this->responsables->find([
            '_id' => ['$in' => array_values($responsableIds)]
        ])->toArray();

        foreach ($responsables as $responsable) {
            $responsablesPorId[(string) $responsable['_id']] = $responsable;
        }
    }

    $resultado = [];

    foreach ($solicitudes as $solicitud) {
        $resultado[] = [
            'solicitud' => $solicitud,
            'estudiante' => $estudiantesPorId[
                (string) $solicitud['estudianteId']
            ] ?? null,
            'responsable' => $solicitud['responsableId'] !== null
                ? ($responsablesPorId[
                    (string) $solicitud['responsableId']
                ] ?? null)
                : null
        ];
    }

    return $resultado;
    }
}
