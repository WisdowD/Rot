<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json; charset=UTF-8");

include("conexao.php");

$raw = file_get_contents("php://input");
$dados = json_decode($raw, true);

if (!$dados || !isset($dados["cart"])) {
    echo json_encode([
        "sucesso" => false,
        "msg" => "JSON inválido",
        "raw" => $raw
    ]);
    exit;
}

$cart = $dados["cart"];
$nome = $dados["nome"];
$telefone = $dados["telefone"];
$tipo = $dados["tipo"];


$rua = !empty($dados["rua"]) ? $dados["rua"] : NULL;
$numero = !empty($dados["numero"]) ? intval($dados["numero"]) : NULL;
$bairro = !empty($dados["bairro"]) ? $dados["bairro"] : NULL;
$obs = !empty($dados["observacao"]) ? $dados["observacao"] : NULL;


$id_cliente = null;



if (isset($_SESSION['user_id'])) {

    $id_cliente = intval($_SESSION['user_id']);

    $stmt = $conn->prepare("UPDATE clientes_tb SET nome = ?, numWhats = ? WHERE id_clientes = ?");
    $stmt->bind_param("ssi", $nome, $telefone, $id_cliente);
    $stmt->execute();
    $stmt->close();

} else {


    $stmt = $conn->prepare("SELECT id_clientes FROM clientes_tb WHERE numWhats = ?");
    $stmt->bind_param("s", $telefone);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {

        $id_cliente = intval($user['id_clientes']);
        $stmt = $conn->prepare("UPDATE clientes_tb SET nome = ? WHERE id_clientes = ?");
        $stmt->bind_param("si", $nome, $id_cliente);
        $stmt->execute();
        $stmt->close();

    } else {
        $stmt = $conn->prepare("INSERT INTO clientes_tb (nome, numWhats, senha) VALUES (?, ?, 'sem_senha')");
        $stmt->bind_param("ss", $nome, $telefone);
        $stmt->execute();
        $id_cliente = $conn->insert_id;
        $stmt->close();
    }
}


if (!$id_cliente) {
    echo json_encode([
        "sucesso" => false,
        "msg" => "Erro fatal ao identificar ou criar cliente."
    ]);
    exit;
}


$total = 0;

$stmt_preco = $conn->prepare("SELECT preco FROM cardapio_tb WHERE id_item = ?");

foreach ($cart as $item) {
    $id_item = intval($item["id"]);
    $qnt = intval($item["qty"]);

    $stmt_preco->bind_param("i", $id_item);
    $stmt_preco->execute();
    $res = $stmt_preco->get_result();
    $row = $res->fetch_assoc();

    if ($row) {
        $total += floatval($row["preco"]) * $qnt;
    }
}
$stmt_preco->close();


$status = 'pendente';

$stmt_pedido = $conn->prepare("
    INSERT INTO pedido_tb 
    (id_clientes, telefone, status, valorTotal, tipoEntrega, Rua, numCasa, Bairro, observacao, dataPedido)
    VALUES 
    (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
");


$stmt_pedido->bind_param(
    "isssdssis",
    $id_cliente,
    $telefone,
    $status,
    $total,
    $tipo,
    $rua,
    $numero,
    $bairro,
    $obs
);
$stmt_pedido->execute();
$id_pedido = $conn->insert_id;
$stmt_pedido->close();

$stmt_item = $conn->prepare("
    INSERT INTO itemPedido_tb (id_pedido, id_item, qnt_item)
    VALUES (?, ?, ?)
");

foreach ($cart as $item) {
    $id_item = intval($item["id"]);
    $qnt = intval($item["qty"]);

    $stmt_item->bind_param("iii", $id_pedido, $id_item, $qnt);
    $stmt_item->execute();
}
$stmt_item->close();


echo json_encode([
    "sucesso" => true,
    "msg" => "Pedido salvo com sucesso!",
    "id_pedido" => $id_pedido
]);
?>