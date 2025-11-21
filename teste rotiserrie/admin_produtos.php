<?php
include("conexao.php");

$filter_categoria = $_GET["categoria"] ?? "todos";

$categorias_ativas = ["carnes", "massas", "acompanhamentos", "sobremesas", "bebidas"];

$sql_counts = "SELECT categoria, COUNT(*) as total FROM cardapio_tb GROUP BY categoria";
$result_counts = $conn->query($sql_counts);

$counts = [];
$total_produtos = 0;
while ($row = $result_counts->fetch_assoc()) {
  $counts[$row['categoria']] = $row['total'];
  $total_produtos += $row['total'];
}

$where_clause = ($filter_categoria === "todos") ? "" : "WHERE c.categoria = '$filter_categoria'";

$sql = "SELECT 
          c.id_item,
          p.nomeProduto,
          p.descricao,
          p.imagem,
          c.preco,
          c.categoria
        FROM cardapio_tb c
        JOIN produtos_tb p ON p.id_produto = c.id_produto
        $where_clause
        ORDER BY c.categoria, p.nomeProduto";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <title>Admin - Produtos</title>
  <link rel="stylesheet" href="styles.css">
  <style>
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

    .btn-edit {
      background: #f39c12;
    }

    .btn-del {
      background: #c0392b;
    }

    .menu-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      justify-content: center;
    }

    .menu-card {
      background: white;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      width: 200px;
      text-align: center;
    }

    .card-content {
      padding: 15px;
    }

    .card-content h3 {
      margin-bottom: 5px;
      color: #1D5131;
    }

    .btn-filter {
      padding: 8px 14px;
      display: inline-block;
      background: #b3c1cbff;
      color: white;
      margin: 3px;
      border-radius: 5px;
      text-decoration: none;
      font-weight: bold;
      transition: background 0.2s;
      text-transform: capitalize;
    }

    .btn-filter.active {
      background: #1D5131;
    }
  </style>
</head>

<body>
  <?php include("admin_header.php"); ?>


  <h1 style="text-align:center;margin-top:20px;">Gerenciar Produtos</h1>

  <div style="max-width:900px;margin:20px auto;text-align:right;">
    <a href="criar_produto.php" class="btn-order"
      style="padding:10px 15px; background: #1D5131; color: white; border-radius: 5px; text-decoration: none;">+ Novo
      Produto</a>
  </div>

  <div
    style="text-align:center; margin-bottom: 20px; max-width: 900px; margin-left: auto; margin-right: auto; padding: 0 20px;">

    <a class="btn-filter <?= $filter_categoria === 'todos' ? 'active' : '' ?>"
      href="admin_produtos.php?categoria=todos">
      Todos (<?= $total_produtos ?>)
    </a>

    <?php foreach ($categorias_ativas as $cat):
      $count = $counts[$cat] ?? 0;
      $is_active = $filter_categoria === $cat;
      ?>
      <a class="btn-filter <?= $is_active ? 'active' : '' ?>" href="admin_produtos.php?categoria=<?= urlencode($cat) ?>">
        <?= ucfirst($cat) ?> (<?= $count ?>)
      </a>
    <?php endforeach; ?>
  </div>


  <div class="menu-grid" style="max-width:900px;margin:auto;">

    <?php while ($row = $result->fetch_assoc()): ?>

      <div class="menu-card">
        <img src="<?= $row['imagem'] ?>" alt="Imagem do Produto" style="width:100%;height:180px;object-fit: cover;">
        <div class="card-content">
          <h3><?= $row['nomeProduto'] ?></h3>
          <p><strong>Categoria:</strong> <?= ucfirst($row['categoria']) ?></p>
          <p><strong>Preço:</strong> R$ <?= number_format($row['preco'], 2, ',', '.') ?></p>
        </div>

        <div style="padding:10px;display:flex;gap:10px;justify-content:center;">

          <a href="editar_produto.php?id_item=<?= $row['id_item'] ?>">
            <button class="btn-small btn-edit">
              Editar
            </button>
          </a>

          <a href="excluir_produto.php?id=<?= $row['id_item'] ?>"
            onclick="return confirm('Você tem certeza que deseja excluir o produto <?= addslashes($row['nomeProduto']) ?>? Essa ação é irreversível.')">

            <button class="btn-small btn-del">
              Excluir
            </button>
          </a>
        </div>
      </div>

    <?php endwhile; ?>

  </div>

</body>

</html>