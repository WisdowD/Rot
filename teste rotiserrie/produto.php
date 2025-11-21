<?php
include("conexao.php");

$id_item = $_GET["id_item"] ?? 0;

$sql = "SELECT 
          c.id_item,
          p.nomeProduto,
          p.descricao,
          p.imagem,
          c.preco
        FROM cardapio_tb c
        JOIN produtos_tb p ON p.id_produto = c.id_produto
        WHERE c.id_item = $id_item";

$result = $conn->query($sql);

echo json_encode($result->fetch_assoc());
?>
