
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

public function registrar(array $datos): void
{
    $this->collection->insertOne([
        'estudianteId' => new MongoDB\BSON\ObjectId($datos['estudianteId']),
        'responsableId' => null,
        'tipo' => $datos['tipo'],
        'titulo' => $datos['titulo'],
        'descripcion' => $datos['descripcion'],
        'prioridad' => $datos['prioridad'],
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

        $resultado = [];

        foreach ($solicitudes as $solicitud) {

            $estudiante = $this->estudiantes->findOne([
                '_id' => $solicitud['estudianteId']
            ]);

            $responsable = null;

            if ($solicitud['responsableId'] !== null) {
                $responsable = $this->responsables->findOne([
                    '_id' => $solicitud['responsableId']
                ]);
            }

            $resultado[] = [
                'solicitud' => $solicitud,
                'estudiante' => $estudiante,
                'responsable' => $responsable
            ];
        }

        return $resultado;
    }
}
