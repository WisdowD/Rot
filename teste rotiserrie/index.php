<?php
session_start();
include("conexao.php");

$is_logged_in = isset($_SESSION['user_id']);

// PEGAR 3 PRODUTOS PARA DESTAQUES
$sql = "SELECT p.nomeProduto, p.descricao, p.imagem, c.preco 
        FROM cardapio_tb c
        JOIN produtos_tb p ON p.id_produto = c.id_produto
        LIMIT 3";
$destaques = $conn->query($sql);

// PEGAR 1 PRODUTO PARA O CARD PRINCIPAL
$sql2 = "SELECT p.nomeProduto, p.descricao, p.imagem, c.preco 
         FROM cardapio_tb c
         JOIN produtos_tb p ON p.id_produto = c.id_produto
         ORDER BY c.id_item DESC LIMIT 1";
$principal = $conn->query($sql2)->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rotisserie Israel — Sabor e Tradição</title>

  <link rel="stylesheet" href="base.css">
  <link rel="stylesheet" href="index.css">

  <link rel="icon" href="img/2.png">
</head>
<body>

<!-- ========================== HEADER ========================== -->
<header class="header">
    <div class="inner">
        <div class="brand">
            <img src="img/2.png" alt="Logo Rotisserie Israel">
            <div>
                <h1>Rotisserie Israel</h1>
                <p>Comida caseira com sabor de família</p>
            </div>
        </div>

        <nav class="nav">
            <?php if ($is_logged_in): ?>
                <a href="index.php">Início</a>
                <a href="cardapio.php">Cardápio</a>
                <a href="logout.php">Sair</a>
            <?php else: ?>
                <a href="index.php">Início</a>
                <a href="login.php">Entrar</a>
            <?php endif; ?>
        </nav>
    </div>
</header>


<!-- ========================== HERO ========================== -->
<section class="hero container">

  <div class="hero-left">

    <h1 class="hero-title">
      Sabor autêntico,
      <span class="laranja">tradição e carinho</span>
    </h1>

    <p class="hero-sub">
      Pratos caseiros preparados com amor, tradição e qualidade.
    </p>

    <div class="hero-buttons">
      <a href="cardapio.php" class="btn-primary">Ver cardápio completo</a>
      <a href="#destaques" class="btn-ghost">Promoções</a>
    </div>
  </div>

  <div class="hero-right">
    <div class="principal-card">
      <img src="<?= $principal['imagem'] ?>" class="principal-img">

      <div class="principal-info">
        <h3><?= $principal['nomeProduto'] ?></h3>
        <p><?= $principal['descricao'] ?></p>
        <strong class="price">R$ <?= number_format($principal['preco'],2,',','.') ?></strong>
      </div>

      <span class="badge-floating">Mais Pedido</span>
    </div>
  </div>

</section>



<!-- ========================== DESTAQUES ========================== -->
<section id="destaques" class="destaques container">

  <h2 class="section-title">Destaques do Dia</h2>

  <div class="destaques-grid">

    <?php while ($row = $destaques->fetch_assoc()): ?>
      <div class="dest-card">
        <img src="<?= $row['imagem'] ?>" class="dest-img">
        <div class="dest-body">
          <h3><?= $row['nomeProduto'] ?></h3>
          <p><?= $row['descricao'] ?></p>
          <span class="price">R$ <?= number_format($row['preco'],2,',','.') ?></span>
        </div>
      </div>
    <?php endwhile; ?>

  </div>

</section>


<!-- =========================== FOOTER =========================== -->
<footer class="footer">
  <p>Desenvolvido por <a href="https://curupacu.github.io/novatecc/" target="_blank">NovaTec</a></p>
</footer>

</body>
</html>
