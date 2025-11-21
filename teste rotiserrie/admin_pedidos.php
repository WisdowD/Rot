<?php
include("conexao.php");

$filter_status = $_GET["status"] ?? "todos";


$statuses = ["pendente", "confirmado", "em preparo", "pronto", "entregue", "cancelado"];

$sql_counts = "SELECT status, COUNT(*) as total FROM pedido_tb GROUP BY status";
$result_counts = $conn->query($sql_counts);

$counts = [];
$total_pedidos = 0;
while ($row = $result_counts->fetch_assoc()) {
    $counts[$row['status']] = $row['total'];
    $total_pedidos += $row['total'];
}



$where_clause = ($filter_status === "todos") ? "" : "WHERE p.status = '$filter_status'";


$sql = "SELECT p.id_pedido, p.id_clientes, p.status, p.valorTotal, p.dataPedido,
               c.nome AS nomeCliente
        FROM pedido_tb p
        JOIN clientes_tb c ON c.id_clientes = p.id_clientes
        $where_clause
        ORDER BY p.id_pedido DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Painel - Pedidos</title>
    <link rel="stylesheet" href="styles.css">

    <style>
        .table {
            width: 95%;
            margin: 30px auto;
            border-collapse: collapse;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
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

        .btn-filter {
            padding: 8px 14px;
            display: inline-block;
            background: #1D5131;
            color: white;
            margin: 3px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.2s;
        }

        .btn-filter.active {
            background: #ff6b35;
        }

        .status {
            padding: 5px 10px;
            border-radius: 8px;
            color: #fff;
            font-weight: bold;
            text-transform: capitalize;
            font-size: 14px;
        }

        .status.pendente {
            background: #c0392b;
        }

        .status.confirmado {
            background: #3498db;
        }

        .status.em-preparo {
            background: #f39c12;
        }

        .status.pronto {
            background: #1abc9c;
        }

        .status.entregue {
            background: #27ae60;
        }

        .status.cancelado {
            background: #7f8c8d;
        }

        .btn-small {
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: bold;
            color: white;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-view {
            background: #3498db;
        }

        .btn-edit {
            background: #f39c12;
        }

        .btn-del {
            background: #c0392b;
        }

        .modal-status {
            display: none;
            position: fixed;
            z-index: 100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.7);
            align-items: center;
            justify-content: center;
        }

        .modal-content-status {
            background-color: #fefefe;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 450px;
            text-align: center;
        }

        .modal-content-status h2 {
            margin-bottom: 20px;
            color: #1D5131;
        }

        .modal-status-body {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }

        .close-modal-btn {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
            margin-top: -10px;
        }

        .close-modal-btn:hover,
        .close-modal-btn:focus {
            color: #c0392b;
            text-decoration: none;
        }

        .modal-content-status .btn {
            padding: 10px 16px;
            display: inline-block;
            background: #1D5131;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            text-transform: capitalize;
            font-weight: bold;
            transition: background 0.2s;
        }

        .modal-content-status .btn:hover {
            background: #2e7a46;
        }
    </style>

</head>

<body>
    <?php include("admin_header.php"); ?>


    <h1 style="text-align:center; margin-top:20px;">Pedidos Recebidos</h1>

    <div
        style="text-align:center; margin-bottom: 20px; max-width: 1200px; margin-left: auto; margin-right: auto; padding: 0 20px;">

        <a class="btn-filter <?= $filter_status === 'todos' ? 'active' : '' ?>" href="admin_pedidos.php?status=todos">
            Todos (<?= $total_pedidos ?>)
        </a>

        <?php foreach ($statuses as $st):
            $count = $counts[$st] ?? 0;
            $is_active = $filter_status === $st;
            ?>
            <a class="btn-filter <?= $is_active ? 'active' : '' ?>" href="admin_pedidos.php?status=<?= urlencode($st) ?>">
                <?= ucfirst($st) ?> (<?= $count ?>)
            </a>
        <?php endforeach; ?>
    </div>

    <table class="table">
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Status</th>
            <th>Valor</th>
            <th>Data</th>
            <th>Ações</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id_pedido'] ?></td>
                <td><?= $row['nomeCliente'] ?></td>

                <td>
                    <?php
                    $status_class = str_replace(' ', '-', strtolower($row['status']));
                    ?>
                    <span class="status <?= $status_class ?>">
                        <?= $row['status'] ?>
                    </span>
                </td>

                <td>R$ <?= number_format($row['valorTotal'], 2, ',', '.') ?></td>
                <td><?= date("d/m/Y H:i", strtotime($row["dataPedido"])) ?></td>

                <td style="display:flex; gap:8px;">
                    <a href="ver_itens.php?id_pedido=<?= $row['id_pedido'] ?>">
                        <button class="btn-small btn-view">Ver Itens</button>
                    </a>

                    <button class="btn-small btn-edit" onclick="openStatusModal(<?= $row['id_pedido'] ?>)">
                        Status
                    </button>

                    <a href="excluir_pedido.php?id_pedido=<?= $row['id_pedido'] ?>"
                        onclick="return confirm('Tem certeza que deseja excluir?')">
                        <button class="btn-small btn-del">Excluir</button>
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>

    </table>

    <div id="statusModal" class="modal-status">
        <div class="modal-content-status">
            <span class="close-modal-btn" onclick="closeStatusModal()">&times;</span>
            <h2 id="modalTitle">Atualizar Status do Pedido #?</h2>
            <div id="modalStatusBody" class="modal-status-body">
                Carregando...
            </div>
        </div>
    </div>
    <script>
        const modal = document.getElementById('statusModal');
        const modalBody = document.getElementById('modalStatusBody');
        const modalTitle = document.getElementById('modalTitle');

        function openStatusModal(pedidoId) {
            modalTitle.textContent = `Atualizar Status do Pedido #${pedidoId}`;
            modalBody.innerHTML = 'Carregando...';
            modal.style.display = 'flex';

            fetch(`atualizar_status.php?id_pedido=${pedidoId}`)
                .then(response => response.text())
                .then(html => {
                    modalBody.innerHTML = html;
                })
                .catch(error => {
                    modalBody.innerHTML = 'Erro ao carregar status.';
                    console.error('Erro ao carregar status:', error);
                });
        }

        function closeStatusModal() {
            modal.style.display = 'none';
        }

        window.onclick = function (event) {
            if (event.target == modal) {
                closeStatusModal();
            }
        }
    </script>

</body>

</html>