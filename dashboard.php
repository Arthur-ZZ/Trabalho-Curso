<?php

session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$nome = $_SESSION["nome"];
$email = $_SESSION["email"];
$nivel = $_SESSION["nivel"];

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - CadLog</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <div class="dashboard">

        <header>

            <h1>CadLog</h1>

            <a href="logout.php">Sair</a>

        </header>

        <main>

            <h2>Olá, <?php echo htmlspecialchars($nome); ?>!</h2>

            <p>Bem-vindo ao sistema.</p>

            <div class="info">

                <h3>Seus dados</h3>

                <p>
                    <strong>Nome:</strong>
                    <?php echo htmlspecialchars($nome); ?>
                </p>

                <p>
                    <strong>E-mail:</strong>
                    <?php echo htmlspecialchars($email); ?>
                </p>

                <p>
                    <strong>Nível:</strong>
                    <?php echo htmlspecialchars($nivel); ?>
                </p>

            </div>

            <?php if ($nivel == "admin") { ?>

                <div class="admin-area">

                    <h3>Área do administrador</h3>

                    <p>
                        Você possui acesso de administrador.
                    </p>

                    <a href="usuarios.php">
                        Gerenciar usuários
                    </a>

                </div>

            <?php } ?>

        </main>

    </div>

</body>

</html>