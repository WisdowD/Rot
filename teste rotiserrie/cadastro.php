<?php
session_start();
include("conexao.php");

$feedback = "";
$nome = $email = $telefone = "";


if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = $_POST["nome"] ?? "";
    $email = $_POST["email"] ?? "";
    $senha_limpa = $_POST["senha"] ?? "";
    $telefone = $_POST["telefone"] ?? "";


    if (empty($nome) || empty($email) || empty($senha_limpa)) {
        $feedback = "<p class='error-message'>Preencha todos os campos obrigatórios (Nome, E-mail, Senha).</p>";
    } else {

        $check_sql = $conn->prepare("SELECT id_clientes FROM clientes_tb WHERE email = ?");
        $check_sql->bind_param("s", $email);
        $check_sql->execute();
        $check_sql->store_result();

        if ($check_sql->num_rows > 0) {
            $feedback = "<p class='error-message'>Este e-mail já está cadastrado.</p>";
        } else {

            $senha_hash = password_hash($senha_limpa, PASSWORD_DEFAULT);


            $insert_sql = $conn->prepare("
                INSERT INTO clientes_tb (nome, email, numWhats, senha) 
                VALUES (?, ?, ?, ?)
            ");
            $insert_sql->bind_param("ssss", $nome, $email, $telefone, $senha_hash);

            if ($insert_sql->execute()) {
                $feedback = "<p class='success-message'>Cadastro realizado com sucesso! Você pode fazer login.</p>";
                $nome = $email = $telefone = "";
            } else {
                $feedback = "<p class='error-message'>Erro ao cadastrar: " . $conn->error . "</p>";
            }
            $insert_sql->close();
        }
        $check_sql->close();
    }
}
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro — Rotisserie Israel</title>
    <link rel="stylesheet" href="./base.css">
    <link rel="stylesheet" href="./login.css">

    <style>
        .error-message {
            color: #c0392b;
            font-weight: bold;
            padding: 10px;
            margin-bottom: 15px;
            background: #fdf2f2;
            border: 1px solid #c0392b;
            border-radius: 6px;
        }

        .success-message {
            color: #1D5131;
            font-weight: bold;
            padding: 10px;
            margin-bottom: 15px;
            background: #f0fff4;
            border: 1px solid #1D5131;
            border-radius: 6px;
        }
    </style>

    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#1D5131">
    <link rel="icon" href="/icons/icon-192.png">
    <link rel="apple-touch-icon" href="./assets/icons/icon-192.png">
</head>

<body class="login">

    <header class="header">
        <div class="header-content"
            style="max-width:1200px;margin:auto;padding:25px 20px; display: flex; justify-content: space-between; align-items: center;">

            <div style="display: flex; align-items: center; gap: 10px;">
                <img src="img/2.png" alt="Logo" style="height:40px;border-radius:6px;">
                <div class="brand" style="margin-bottom: 0;">ROTISSERIE ISRAEL</div>
            </div>

            <nav class="nav">
                <a href="index.php" style="
            text-decoration: none; 
            background: #1a472a; 
            color: white; 
            padding: 8px 16px; 
            border-radius: 8px; 
            font-weight: bold; 
            display: inline-block;">
                    Início
                </a>
            </nav>
        </div>
    </header>

    <main class="auth">
        <section class="auth__card">
            <div class="auth__header">
                <h1>Crie sua conta</h1>
                <p class="auth__subtitle">É rápido e fácil para começar a fazer seus pedidos</p>
            </div>

            <?php echo $feedback; ?>

            <form method="POST" class="auth__form">

                <div class="field">
                    <label for="nome" class="label">Nome Completo</label>
                    <input type="text" id="nome" name="nome" class="input" placeholder="Seu nome completo" required
                        autocomplete="name" value="<?= htmlspecialchars($nome) ?>">
                </div>

                <div class="field">
                    <label for="email" class="label">E-mail</label>
                    <input type="email" id="email" name="email" class="input" placeholder="voce@email.com" required
                        autocomplete="email" value="<?= htmlspecialchars($email) ?>">
                </div>

                <div class="field">
                    <label for="senha" class="label">Senha</label>
                    <input type="password" id="senha" name="senha" class="input" placeholder="Crie uma senha forte"
                        required autocomplete="new-password">
                </div>

                <div class="field">
                    <label for="telefone" class="label">Telefone (WhatsApp)</label>
                    <input type="text" id="telefone" name="telefone" class="input" placeholder="(99) 99999-9999"
                        autocomplete="tel" value="<?= htmlspecialchars($telefone) ?>">
                </div>

                <button class="btn btn--primary" type="submit">
                    <span class="btn__text">Cadastrar</span>
                    <span class="btn__spinner" aria-hidden="true"></span>
                </button>
            </form>

            <div class="auth__footer">
                Já tem uma conta? <a href="login.php" class="link">Fazer Login</a>
            </div>

        </section>
    </main>
</body>

</html>