<?php

include __DIR__ . "/config/conexao.php";

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];
    $data_nascimento = $_POST["data_nascimento"];

    $sql = "SELECT id FROM usuarios WHERE email = ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {

        $mensagem = "Este e-mail já está cadastrado!";

    } else {

        $sql = "INSERT INTO usuarios (nome, email, senha, nivel, data_nascimento)
                VALUES (?, ?, ?, 'cliente', ?)";

        $stmt = $conexao->prepare($sql);

        $stmt->bind_param(
            "ssss",
            $nome,
            $email,
            $senha,
            $data_nascimento
        );

        if ($stmt->execute()) {

            $mensagem = "Cadastro realizado com sucesso!";

        } else {

            $mensagem = "Erro ao realizar o cadastro.";

        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <title>Cadastro - CadLog</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <div class="login-box">

        <h1>CadLog</h1>

        <h2>Cadastrar</h2>

        <?php if ($mensagem != "") { ?>

            <p>
                <?php echo $mensagem; ?>
            </p>

        <?php } ?>

        <form method="POST">

            <label>Nome</label>

            <input
                type="text"
                name="nome"
                required
            >

            <label>E-mail</label>

            <input
                type="email"
                name="email"
                required
            >

            <label>Senha</label>

            <input
                type="password"
                name="senha"
                required
            >

            <label>Data de nascimento</label>

            <input
                type="date"
                name="data_nascimento"
                required
            >

            <button type="submit">
                Cadastrar
            </button>

        </form>

        <p>
            Já possui uma conta?
            <a href="login.php">Voltar para o login</a>
        </p>

    </div>

</body>

</html>