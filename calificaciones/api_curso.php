<?php
session_start();
if (!isset($_SESSION['docente'])) { http_response_code(401); exit('Unauthorized'); }
require_once(__DIR__ . "/../config/conexion.php");

header('Content-Type: application/json; charset=utf-8');

$id_curso = (int)($_GET['id_curso'] ?? 0);
if ($id_curso <= 0) { echo json_encode(['error' => 'id_curso requerido']); exit(); }

// Evaluaciones del curso
$evResult = pg_query($conexion,
    "SELECT id_evaluacion, descripcion, porcentaje, posicion
     FROM evaluacion
     WHERE id_curso = $id_curso
     ORDER BY posicion");
$evaluaciones = [];
while ($row = pg_fetch_assoc($evResult)) $evaluaciones[] = $row;

// Estudiantes inscritos en el curso
$estResult = pg_query($conexion,
    "SELECT i.id_inscripcion, e.cod_estudiante, e.nombres, e.apellidos
     FROM inscripcion i
     INNER JOIN estudiante e ON i.cod_estudiante = e.cod_estudiante
     WHERE i.id_curso = $id_curso
     ORDER BY e.apellidos, e.nombres");
$estudiantes = [];
while ($row = pg_fetch_assoc($estResult)) $estudiantes[] = $row;

// Calificaciones ya existentes en este curso
// Para que el front pueda detectar duplicados ANTES de enviar el form
$calResult = pg_query($conexion,
    "SELECT c.id_inscripcion, c.id_evaluacion
     FROM calificacion c
     INNER JOIN inscripcion i ON c.id_inscripcion = i.id_inscripcion
     WHERE i.id_curso = $id_curso");
$calificaciones_existentes = [];
while ($row = pg_fetch_assoc($calResult)) {
    $calificaciones_existentes[] = [
        'id_inscripcion' => (int)$row['id_inscripcion'],
        'id_evaluacion'  => (int)$row['id_evaluacion'],
    ];
}

echo json_encode(compact('evaluaciones', 'estudiantes', 'calificaciones_existentes'));
