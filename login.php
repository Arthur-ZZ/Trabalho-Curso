<?php

session_start();

include "config/conexao.php";

$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows == 1) {

        $usuario = $resultado->fetch_assoc();

        if (password_verify($senha, $usuario["senha"])) {

            $_SESSION["usuario_id"] = $usuario["id"];
            $_SESSION["nome"] = $usuario["nome"];
            $_SESSION["email"] = $usuario["email"];
            $_SESSION["nivel"] = $usuario["nivel"];

            header("Location: dashboard.php");
            exit;

        } else {
            $erro = "Senha incorreta!";
        }

    } else {
        $erro = "Usuário não encontrado!";
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <title>Login - CadLog</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <div class="login-box">

        <h1>CadLog</h1>

        <h2>Login</h2>

        <?php if ($erro != "") { ?>

            <p class="erro">
                <?php echo $erro; ?>
            </p>

        <?php } ?>

        <form method="POST">

            <label>E-mail</label>
            <input type="email" name="email" required>

            <label>Senha</label>
            <input type="password" name="senha" required>

            <button type="submit">Entrar</button>

        </form>

        <p>
            Ainda não possui uma conta?
            <a href="cadastrar.php">Cadastre-se</a>
        </p>

    </div>

</body>

</html>