<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("conexao.php");

if (!isset($_GET["id_item"])) {
    die("ID do produto não informado!");
}

$id_item = intval($_GET["id_item"]);

$sql = "SELECT 
            c.id_item, c.id_produto, c.preco, c.categoria,
            p.nomeProduto, p.descricao, p.imagem
        FROM cardapio_tb c
        JOIN produtos_tb p ON p.id_produto = c.id_produto
        WHERE c.id_item = $id_item";

$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Produto não encontrado!");
}

$produto = $result->fetch_assoc();

// FORM ENVIADO
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = $_POST["nome"];
    $descricao = $_POST["descricao"];
    $imagem = $_POST["imagem"];
    $preco = $_POST["preco"];
    $categoria = $_POST["categoria"];

    $id_produto = $produto["id_produto"];

    // Atualiza produtos_tb
    $conn->query("
        UPDATE produtos_tb
        SET nomeProduto='$nome',
            descricao='$descricao',
            imagem='$imagem'
        WHERE id_produto=$id_produto
    ");

    // Atualiza cardapio_tb
    $conn->query("
        UPDATE cardapio_tb
        SET preco='$preco',
            categoria='$categoria'
        WHERE id_item=$id_item
    ");

    header("Location: admin_produtos.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Editar Produto</title>
    <link rel="stylesheet" href="styles.css">

    <style>
        .form-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 550px;
            margin: auto;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .form-box label {
            display: block;
            margin-top: 12px;
            font-weight: bold;
        }

        .form-box input,
        .form-box select,
        .form-box textarea {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 15px;
        }

        textarea {
            resize: vertical;
            height: 90px;
        }

        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <?php include("admin_header.php"); ?>


    <h1 style="text-align:center;margin-top:30px;">Editar Produto</h1>

    <div class="form-box">

        <form method="POST">

            <label>Nome</label>
            <input type="text" name="nome" value="<?= $produto['nomeProduto'] ?>" required>

            <label>Descrição</label>
            <textarea name="descricao" required><?= $produto['descricao'] ?></textarea>

            <label>Categoria</label>
            <select name="categoria" required>
                <option value="carnes" <?= $produto['categoria'] == "carnes" ? "selected" : "" ?>>Carnes</option>
                <option value="massas" <?= $produto['categoria'] == "massas" ? "selected" : "" ?>>Massas</option>
                <option value="acompanhamentos" <?= $produto['categoria'] == "acompanhamentos" ? "selected" : "" ?>>
                    Acompanhamentos</option>
                <option value="sobremesas" <?= $produto['categoria'] == "sobremesas" ? "selected" : "" ?>>Sobremesas</option>
                <option value="bebidas" <?= $produto['categoria'] == "bebidas" ? "selected" : "" ?>>Bebidas</option>
            </select>

            <label>Preço</label>
            <input type="text" name="preco" value="<?= $produto['preco'] ?>" required>

            <label>Imagem (URL)</label>
            <input type="text" name="imagem" value="<?= $produto['imagem'] ?>">

            <div class="btn-container">
                <button class="btn-order" style="flex:1;margin-right:10px;">Salvar Alterações</button>
                <a href="admin_produtos.php" class="btn-close"
                    style="flex:1;text-align:center;padding:10px 0;text-decoration: none;">Voltar</a>
            </div>

        </form>

    </div>

</body>

</html>