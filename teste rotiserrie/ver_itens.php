<?php
include("conexao.php");

$id = $_GET["id_pedido"] ?? 0;

// Buscar o pedido
$resultado_pedido = $conn->query("
  SELECT p.*, c.nome, p.telefone, p.Rua, p.numCasa, p.Bairro, p.observacao, p.tipoEntrega, p.valorTotal
  FROM pedido_tb p
  JOIN clientes_tb c ON c.id_clientes = p.id_clientes
  WHERE p.id_pedido = $id
");

if (!$resultado_pedido || $resultado_pedido->num_rows === 0) {
  die("
        <!DOCTYPE html><html lang='pt-BR'><head><meta charset='UTF-8'><title>Erro</title><link rel='stylesheet' href='styles.css'></head>
        <body style='padding:20px;'>
          <div class='menu-card card-content' style='max-width:500px; margin:50px auto; text-align:center;'>
            <h1>Erro</h1>
            <p style='margin:10px 0;'>Pedido #$id não encontrado ou ID inválido.</p>
            <a href='admin_pedidos.php' class='nav-btn' style='display:inline-block;'>⬅ Voltar</a>
          </div>
        </body></html>
    ");
}

$pedido = $resultado_pedido->fetch_assoc();

// Buscar itens 
$itens = $conn->query("
  SELECT i.qnt_item, p.nomeProduto, c.preco 
  FROM itemPedido_tb i
  JOIN cardapio_tb c ON c.id_item = i.id_item
  JOIN produtos_tb p ON p.id_produto = c.id_produto
  WHERE i.id_pedido = $id
");

// Define a classe CSS do status
$statusClass = str_replace(' ', '', strtolower($pedido['status'] ?? 'pendente'));

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <title>Itens do Pedido #<?= $id ?></title>
  <link rel="stylesheet" href="styles.css">
  <style>
    .info-container {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 25px;
      margin-bottom: 25px;
    }

    .info-box {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .info-box h2 {
      color: #1a472a;
      margin-bottom: 15px;
    }

    .status {

      padding: 5px 10px;
      border-radius: 8px;
      color: #fff;
      font-weight: bold;
      text-transform: capitalize;
      font-size: 14px;
      display: inline-block;
    }

    .status.pendente {
      background: #c0392b;
    }

    .status.preparando {
      background: #f39c12;
    }

    .status.acaminho {
      background: #2980b9;
    }

    .status.entregue {
      background: #27ae60;
    }

    .status.cancelado {
      background: #7f8c8d;
    }


    .table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    .table th {
      background: #1D5131;
      color: white;
      padding: 12px;
      font-size: 16px;
    }

    .table td {
      padding: 12px;
      border-bottom: 1px solid #eee;
    }

    .total-box {
      background: #e6e6e6;
      padding: 15px;
      border-radius: 8px;
      margin-top: 20px;
      text-align: right;
      font-size: 1.5rem;
      font-weight: bold;
      color: #1a472a;
    }
  </style>
</head>

<body>

  <?php include("admin_header.php"); ?>

  <div class="nav-container" style="max-width: 900px; margin-top: 30px;">
    <h1> Pedido #<?= $id ?>
      <span class="status <?= $statusClass ?>">
        <?= $pedido['status'] ?>
      </span>
    </h1>
    <a href="admin_pedidos.php" class="nav-btn">⬅ Voltar</a>
  </div>

  <div class="nav-container info-container" style="max-width: 900px; margin-top: 20px;">

    <div class="info-box">
      <h2>Dados do Cliente</h2>
      <p><strong>Nome:</strong> <?= $pedido['nome'] ?></p>
      <p><strong>Telefone:</strong> <?= $pedido['telefone'] ?></p>
      <p><strong>Tipo de Entrega:</strong> <?= ucfirst($pedido['tipoEntrega']) ?></p>
      <p><strong>Observação:</strong> <?= !empty($pedido['observacao']) ? $pedido['observacao'] : 'Nenhuma' ?></p>
    </div>

    <div class="info-box">
      <h2>🏠 Endereço de Entrega</h2>
      <?php if ($pedido['tipoEntrega'] === 'entrega'): ?>
        <p><strong>Rua:</strong> <?= $pedido['Rua'] ?? 'Não informado' ?></p>
        <p><strong>Número:</strong> <?= $pedido['numCasa'] ?? 'S/N' ?></p>
        <p><strong>Bairro:</strong> <?= $pedido['Bairro'] ?? 'Não informado' ?></p>
      <?php else: ?>
        <p>O pedido será **retirado no local**.</p>
      <?php endif; ?>
    </div>

  </div>
  <div class="nav-container" style="max-width: 900px;">
    <div class="info-box" style="width:100%;">
      <h2>🛒 Itens do Pedido</h2>

      <table class="table">
        <tr>
          <th>Produto</th>
          <th>Qtd</th>
          <th>Preço Unitário</th>
          <th>Subtotal</th>
        </tr>

        <?php $totalItens = 0;
        while ($row = $itens->fetch_assoc()):
          $subtotal = $row['preco'] * $row['qnt_item'];
          $totalItens += $subtotal;
          ?>
          <tr>
            <td><?= $row['nomeProduto'] ?></td>
            <td><?= $row['qnt_item'] ?></td>
            <td>R$ <?= number_format($row['preco'], 2, ',', '.') ?></td>
            <td>R$ <?= number_format($subtotal, 2, ',', '.') ?></td>
          </tr>
        <?php endwhile; ?>
      </table>

      <div class="total-box">
        Total: R$ <?= number_format($pedido['valorTotal'], 2, ',', '.') ?>
      </div>

    </div>
  </div>


</body>

</html>