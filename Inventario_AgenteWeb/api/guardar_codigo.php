<?php

require_once __DIR__ . "/../config/conexion.php";

$data = json_decode(file_get_contents("php://input"), true);

$id = $data["id"] ?? null;
$codigo = trim($data["codigo_inventario"] ?? "");

// validar
if (!$id || !$codigo) {
    echo json_encode(["status" => "error", "msg" => "Datos incompletos"]);
    exit;
}

// opcional: validar formato también en backend
if (!preg_match('/^\d{2}-\d{2}-\d{2}-\d{4}-\d{5}$/', $codigo)) {
    echo json_encode(["status" => "error", "msg" => "Formato inválido"]);
    exit;
}

// actualizar
$sql = "UPDATE equipos SET codigo_inventario = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $codigo, $id);

if ($stmt->execute()) {
    echo json_encode(["status" => "ok", "msg" => "Código guardado"]);
} else {
    echo json_encode(["status" => "error", "msg" => "Error al guardar"]);
}

$conn->close();