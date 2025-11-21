<?php
include("conexao.php");

$id = $_GET["id"];

$conn->query("DELETE FROM cardapio_tb WHERE id_item = $id");

header("Location: admin_produtos.php");
exit;
?>