<?php
include("conexao.php");

$id = $_GET["id_pedido"];

// 1) Apaga os itens do pedido primeiro (por causa da FK)
$conn->query("DELETE FROM itemPedido_tb WHERE id_pedido = $id");

// 2) Apaga o pedido em si
$conn->query("DELETE FROM pedido_tb WHERE id_pedido = $id");

// volta pra lista
header("Location: admin_pedidos.php");
exit;
?>