<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include("conexao.php");

$id_cliente = $_SESSION['user_id'];

$sql = "SELECT * FROM pedido_tb 
        WHERE id_clientes = $id_cliente 
        ORDER BY id_pedido DESC";

$result = $conn->query($sql);

$pedidos = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pedidos[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Meus Pedidos</title>

<style>
body {
    background: #d8cabb;
    font-family: 'Segoe UI', sans-serif;
    padding: 0;
    margin: 0;
}

.container {
    max-width: 900px;
    margin: 40px auto;
    background: #fff8ee;
    padding: 30px;
    border-radius: 18px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

h1 {
    text-align: center;
    color: #497157;
    margin-bottom: 25px;
    font-size: 2rem;
}

.pedido-card {
    background: #fff6e6;
    border-left: 6px solid #497157;
    padding: 18px;
    border-radius: 12px;
    margin-bottom: 18px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.10);
}

.pedido-card h3 {
    margin-bottom: 10px;
    color: #1a472a;
}

.pedido-card p {
    margin: 4px 0;
    color: #333;
}

.btn-voltar {
    display: inline-block;
    padding: 10px 18px;
    background: #497157;
    color: #fff;
    text-decoration: none;
    border-radius: 10px;
    margin-bottom: 20px;
}
</style>
</head>

<body>

<div class="container">

    <a href="cardapio.php" class="btn-voltar">← Voltar ao Cardápio</a>

    <h1>📦 Meus Pedidos</h1>

    <?php if (empty($pedidos)): ?>
        <p style="text-align:center; font-size:1.2rem; color:#666;">
            Você ainda não fez nenhum pedido.
        </p>

    <?php else: ?>

        <?php foreach ($pedidos as $row): ?>
            <div class="pedido-card">

                <h3>Pedido #<?= $row["id_pedido"] ?></h3>

                <p><strong>Total:</strong>
                    R$ <?= number_format($row["valorTotal"] ?? 0, 2, ',', '.') ?>
                </p>

                <p><strong>Status:</strong> <?= $row["status"] ?></p>

                <p><strong>Data:</strong>
                    <?= date("d/m/Y H:i", strtotime($row["dataPedido"])) ?>
                </p>

            </div>
        <?php endforeach; ?>

    <?php endif; ?>

</div>

</body>
</html>
