<?php
session_start();

// Se não estiver logado, manda pro login
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

include("conexao.php");

// Dados do cliente logado
$cliente_nome = "";
$cliente_telefone = "";

$id_cliente = $_SESSION['user_id'];

$sql_cliente = "SELECT nome, numWhats FROM clientes_tb WHERE id_clientes = ?";
$stmt = $conn->prepare($sql_cliente);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$result_cliente = $stmt->get_result();

if ($result_cliente && $result_cliente->num_rows > 0) {
  $cliente_data = $result_cliente->fetch_assoc();
  $cliente_nome = $cliente_data['nome'] ?? "";
  $cliente_telefone = $cliente_data['numWhats'] ?? "";
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Rotisserie Israel - Cardápio Digital</title>

  <!-- CSS Marronzinho -->
  <link rel="stylesheet" href="cardapio.css">

  


  <!-- CSS do modal de acessibilidade -->
  <link rel="stylesheet" href="acessibilidade.css">
</head>

<body>

<!-- ====================== BARRA DE ACESSIBILIDADE ====================== -->
<div class="accessibility-bar">
    <button id="btn-accessibility" class="accessibility-btn">♿ Acessibilidade</button>
</div>

<!-- ====================== HEADER ====================== -->
<header class="header">
    <img src="img/2.png" alt="Logo Rotisserie Israel">

    <div class="header-text">
      <h1 class="logo">Rotisserie Israel</h1>
      <p class="tagline">Comida caseira com sabor de família 🍽️</p>
    </div>

    <div class="header-actions">
      <a href="index.php" class="header-btn">Início</a>
      <a href="logout.php" class="header-btn">Sair</a>
    </div>
</header>

<!-- ====================== NAVBAR ====================== -->
<nav class="navbar">
  <div class="nav-container">

    <div class="nav-buttons">
      <a href="cardapio.php?categoria=todos" class="nav-btn">Todos</a>
      <a href="cardapio.php?categoria=carnes" class="nav-btn">Carnes</a>
      <a href="cardapio.php?categoria=massas" class="nav-btn">Massas</a>
      <a href="cardapio.php?categoria=acompanhamentos" class="nav-btn">Acompanhamentos</a>
      <a href="cardapio.php?categoria=sobremesas" class="nav-btn">Sobremesas</a>
      <a href="cardapio.php?categoria=bebidas" class="nav-btn">Bebidas</a>
      <a href="meus_pedidos.php" class="nav-btn" style="font-weight:bold;">📦 Meus Pedidos</a>
    </div>

    <button id="cart-btn" class="cart-btn-navbar">
      🛒 <span id="cart-count">0</span>
    </button>

  </div>
</nav>

<!-- ====================== CONTEÚDO ====================== -->
<main class="main-content">
    <section class="menu-section">
      <h2 id="section-title">Produtos</h2>

      <!-- BARRA DE PESQUISA -->
<div class="search-box">
    <input type="text" id="searchInput" placeholder="Pesquisar pratos..." autocomplete="off">
</div>


      <?php
      // Carregar itens do cardápio
      $cat = $_GET["categoria"] ?? "todos";

      if ($cat === "todos") {
        $sql = "SELECT c.id_item, p.nomeProduto, p.descricao, p.imagem, c.preco, c.categoria
                FROM cardapio_tb c
                JOIN produtos_tb p ON p.id_produto = c.id_produto";
        $stmt = $conn->prepare($sql);
      } else {
        $sql = "SELECT c.id_item, p.nomeProduto, p.descricao, p.imagem, c.preco, c.categoria
                FROM cardapio_tb c
                JOIN produtos_tb p ON p.id_produto = c.id_produto
                WHERE c.categoria = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $cat);
      }

      $stmt->execute();
      $result = $stmt->get_result();
      ?>

      <div class="menu-grid">

        <?php while ($row = $result->fetch_assoc()): ?>
       <article class="menu-card" onclick="openProductModal(<?= (int)$row['id_item'] ?>)">
    <img src="<?= htmlspecialchars($row['imagem'] ?: 'img/default.jpg') ?>"
         alt="<?= htmlspecialchars($row['nomeProduto']) ?>">

    <div class="card-content">
        <h3><?= htmlspecialchars($row['nomeProduto']) ?></h3>
        <p class="card-price">R$ <?= number_format((float)$row['preco'], 2, ',', '.') ?></p>

        <!-- BOTÃO DE ACESSIBILIDADE (LER SOMENTE O ITEM) -->
        <button class="btn-read"
                onclick="event.stopPropagation(); lerItem(
                    '<?= htmlspecialchars($row['nomeProduto']) ?>',
                    `<?= htmlspecialchars($row['descricao']) ?>`
                )">
            🔊 Ler Item
        </button>
    </div>
</article>


        <?php endwhile; ?>

      </div>

      <?php $stmt->close(); ?>

    </section>
</main>

<!-- ====================== MODAL DE PRODUTO ====================== -->
<div id="product-modal" class="product-modal">
  <div class="product-content">
    <img id="modal-image" src="">
    <h3 id="modal-name"></h3>
    <p id="modal-description"></p>
    <p id="modal-price" class="modal-price"></p>
    <button id="btn-read-modal" class="btn-read">🔊 Ler Detalhes</button>


    <button id="modal-add-btn" class="btn-order">🛒 Adicionar ao Carrinho</button>
    <button id="close-product" class="btn-close">Fechar</button>
  </div>
</div>

<!-- ====================== MODAL DO CARRINHO ====================== -->
<div id="cart-modal" class="cart-modal">
  <div class="cart-content">

    <h2 class="modal-title">
      <img src="https://cdn-icons-png.flaticon.com/512/891/891462.png" width="28">
      Finalizar Pedido
    </h2>

    <div id="cart-items"></div>

    <p id="cart-total" class="cart-total">Total: R$ 0,00</p>

    <hr>

    <h3 style="text-align:center; color:#497157;">Dados do Cliente</h3>

    <form class="modal-form" id="form-pedido">

      <div class="form-row">
        <input id="cliente-nome" type="text" value="<?= htmlspecialchars($cliente_nome) ?>" required>
        <input id="cliente-telefone" type="text" value="<?= htmlspecialchars($cliente_telefone) ?>" required>
      </div>

      <div class="form-row">
        <select id="cliente-tipo" required>
          <option value="">Tipo de Pedido</option>
          <option value="retirada">Retirada</option>
          <option value="entrega">Entrega</option>
        </select>
      </div>

      <div id="endereco-box" style="display:none;">
        <input id="cliente-rua" type="text" placeholder="Rua">
        <input id="cliente-numero" type="text" placeholder="Número">
        <input id="cliente-bairro" type="text" placeholder="Bairro">
      </div>

      <textarea id="cliente-observacao" rows="3" placeholder="Observação (opcional)"></textarea>

      <div class="modal-buttons">
        <button type="button" id="finalize-btn" class="btn-finalize">Enviar Pedido</button>
        <button type="button" id="close-cart" class="btn-close">Cancelar</button>
      </div>

    </form>

  </div>
</div>

<!-- ====================== MODAL DE ACESSIBILIDADE ====================== -->
<div id="accessibility-modal" class="accessibility-modal">

  <div class="accessibility-content">

    <h2>Opções de Acessibilidade</h2>

  <div class="font-buttons">
    <button id="acc-font-plus" class="font-btn">Aumentar A+</button>
    <button id="acc-font-minus" class="font-btn">Diminuir A−</button>
</div>

    <button id="acc-darkmode">Ativar Dark Mode</button>
    <button id="acc-contrast">Alto Contraste</button>
    <button id="acc-no-animation">Desativar Animações</button>
    <button id="acc-reset">Restaurar Acessibilidade</button>

    <br><br>
    <button id="acc-close" class="acc-close">Fechar</button>
  </div>

</div>

<div id="toast" class="toast">Mensagem</div>


<!-- ====================== SCRIPTS ====================== -->
<script src="script.js"></script>
<script src="pwa-install.js"></script>
<script src="acessibilidade.js"></script>

</body>
</html>
