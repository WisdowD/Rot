<?php
include("conexao.php");

$id = $_GET["id_pedido"] ?? 0;

$result_pedido = $conn->query("SELECT * FROM pedido_tb WHERE id_pedido = $id");
$pedido = $result_pedido->num_rows > 0 ? $result_pedido->fetch_assoc() : null;

$statuses = ["pendente", "confirmado", "em preparo", "pronto", "entregue", "cancelado"];

if ($pedido) {
  echo "<p>Status Atual: <strong>" . ucfirst($pedido['status']) . "</strong></p><br>";

  foreach ($statuses as $st) {
    $is_active_style = $st === $pedido['status'] ? 'style="background: #ff6b35; border: 2px solid #ff6b35;"' : '';

    echo '<a class="btn" ' . $is_active_style . ' href="mudar_status.php?id_pedido=' . $id . '&status=' . urlencode($st) . '">';
    echo ucfirst($st);
    echo '</a>';
  }
} else {
  echo "<p>Pedido não encontrado.</p>";
}
?>