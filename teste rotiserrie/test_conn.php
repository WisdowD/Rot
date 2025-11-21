<?php

$host = "sql310.infinityfree.com";
$user = "if0_40429673";
$pass = "Especial2026";
$db   = "if0_40429673_loja_rotserie_bd";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("ERRO NA CONEXÃO → " . $conn->connect_error);
}

echo "OK → Conectou ao banco!";

?>
