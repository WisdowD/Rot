<?php
include("conexao.php");

$id = $_GET["id_pedido"] ?? 0;
$status = urldecode($_GET["status"] ?? "pendente");

if ($id > 0) {
    $conn->query("UPDATE pedido_tb SET status='$status' WHERE id_pedido=$id");
}

header("Location: admin_pedidos.php");
exit;
?>