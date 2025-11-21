<?php
session_start();
include("conexao.php");

$feedback = "";


if (isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = $_POST["email"] ?? "";
  $senha_limpa = $_POST["senha"] ?? "";

  if (empty($email) || empty($senha_limpa)) {
    $feedback = "<p class='error-message'>Preencha o e-mail e a senha.</p>";
  } else {

    $stmt = $conn->prepare("SELECT id_clientes, nome, senha FROM clientes_tb WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();


    if ($user && password_verify($senha_limpa, $user['senha'])) {
      // Senha Correta
      $_SESSION['user_id'] = $user['id_clientes'];
      $_SESSION['user_name'] = $user['nome'];

      header("Location: index.php");
      exit;
    } else {
      // Senha Incorreta
      $feedback = "<p class='error-message'>E-mail ou senha incorretos.</p>";
    }
  }
}
?>
<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Entrar — Rotisserie Israel</title>
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
        <h1>Acesse sua conta</h1>
        <p class="auth__subtitle">Entre para acessar seu histórico de pedidos e favoritos</p>
      </div>

      <?php echo $feedback; ?>

      <form method="POST" class="auth__form">

        <div class="field">
          <label for="email" class="label">
            E-mail
          </label>
          <input type="email" id="email" name="email" class="input" placeholder="voce@email.com" required
            autocomplete="email">
        </div>

        <div class="field">
          <label for="senha" class="label">
            Senha
          </label>
          <input type="password" id="senha" name="senha" class="input" placeholder="Sua senha secreta" required
            autocomplete="current-password">
        </div>

        <button class="btn btn--primary" type="submit">
          <span class="btn__text">Entrar</span>
          <span class="btn__spinner" aria-hidden="true"></span>
        </button>
      </form>

      <div class="auth__footer">
        Não tem uma conta? <a href="cadastro.php" class="link">Cadastre-se</a>
      </div>

    </section>
  </main>
</body>

</html>