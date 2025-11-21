<?php
include("conexao.php");

// se enviou o formulário
if ($_SERVER["REQUEST_METHOD"] === "POST") {

  $nome = $conn->real_escape_string($_POST["nome"]);
  $descricao = $conn->real_escape_string($_POST["descricao"]);
  $categoria = $conn->real_escape_string($_POST["categoria"]);
  $preco = floatval($_POST["preco"]);
  $imagem = $conn->real_escape_string($_POST["imagem"]);

  // 1) cria produto
  $conn->query("
        INSERT INTO produtos_tb (nomeProduto, descricao, imagem)
        VALUES ('$nome', '$descricao', '$imagem')
    ");

  $id_produto = $conn->insert_id;

  // 2) cria item no cardápio
  $conn->query("
        INSERT INTO cardapio_tb (id_produto, preco, categoria)
        VALUES ($id_produto, $preco, '$categoria')
    ");

  header("Location: admin_produtos.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <title>Novo Produto</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    .product-form {
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .product-form label {
      font-weight: bold;
      color: #333;
      margin-top: 5px;
    }

    .product-form input[type="text"],
    .product-form input[type="number"],
    .product-form select,
    .product-form textarea {
      padding: 12px;
      border: 2px solid #ccc;
      border-radius: 8px;
      font-size: 1rem;
    }

    .product-form textarea {
      resize: vertical;
    }

    .btn-submit {
      background: #1a472a;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 10px;
      font-weight: bold;
      cursor: pointer;
      margin-top: 15px;
    }

    .btn-voltar {
      padding: 10px 50px;
      border-radius: 10px;
      font-weight: bold;
      display: inline-block;
      text-decoration: none;
      background: #ff6b35;
      color: white;
    }
  </style>
</head>

<body>
  <?php include("admin_header.php"); ?>


  <h1 style="text-align:center;margin:20px 0;">Adicionar Novo Produto</h1>

  <div style="max-width:500px;margin:auto;">
    <form method="POST" class="product-form">

      <label>Nome do Produto</label>
      <input type="text" name="nome" required>

      <label>Descrição</label>
      <textarea name="descricao" required></textarea>

      <label>Categoria</label>
      <select name="categoria" required>
        <option value="">Selecione...</option>
        <option value="carnes">Carnes</option>
        <option value="massas">Massas</option>
        <option value="acompanhamentos">Acompanhamentos</option>
        <option value="sobremesas">Sobremesas</option>
        <option value="bebidas">Bebidas</option>
      </select>

      <label>Preço</label>
      <input type="number" name="preco" step="0.01" required>

      <label>Imagem (URL)</label>
      <input type="text" name="imagem">

      <button type="submit" class="btn-submit">Salvar Produto</button>
    </form>

    <div style="margin-top:20px;margin-bottom:20px;text-align:center;">
      <a href="admin_produtos.php" class="btn-voltar">Voltar</a>
    </div>
  </div>

</body>

</html>