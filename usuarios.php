<?php

session_start();

include __DIR__ . "/config/conexao.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION["nivel"] != "admin") {
    header("Location: dashboard.php");
    exit;
}

if (isset($_POST["editar"])) {

    $id = $_POST["id"];
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $nivel = $_POST["nivel"];
    $data_nascimento = $_POST["data_nascimento"];

    if ($id == $_SESSION["usuario_id"]) {
        $nivel = $_SESSION["nivel"];
    }

    $sql = "UPDATE usuarios
            SET nome = ?, email = ?, nivel = ?, data_nascimento = ?
            WHERE id = ?";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param(
        "ssssi",
        $nome,
        $email,
        $nivel,
        $data_nascimento,
        $id
    );

    $stmt->execute();

    header("Location: usuarios.php");
    exit;
}

if (isset($_GET["promover"])) {

    $id = $_GET["promover"];

    if ($id != $_SESSION["usuario_id"]) {

        $sql = "UPDATE usuarios
                SET nivel = 'admin'
                WHERE id = ?";

        $stmt = $conexao->prepare($sql);

        $stmt->bind_param("i", $id);

        $stmt->execute();
    }

    header("Location: usuarios.php");
    exit;
}

if (isset($_GET["rebaixar"])) {

    $id = $_GET["rebaixar"];

    if ($id != $_SESSION["usuario_id"]) {

        $sql = "UPDATE usuarios
                SET nivel = 'cliente'
                WHERE id = ?";

        $stmt = $conexao->prepare($sql);

        $stmt->bind_param("i", $id);

        $stmt->execute();
    }

    header("Location: usuarios.php");
    exit;
}

if (isset($_GET["excluir"])) {

    $id = $_GET["excluir"];

    if ($id != $_SESSION["usuario_id"]) {

        $sql = "DELETE FROM usuarios WHERE id = ?";

        $stmt = $conexao->prepare($sql);

        $stmt->bind_param("i", $id);

        $stmt->execute();
    }

    header("Location: usuarios.php");
    exit;
}

$usuario_editar = null;

if (isset($_GET["editar"])) {

    $id = $_GET["editar"];

    $sql = "SELECT * FROM usuarios WHERE id = ?";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $resultado_editar = $stmt->get_result();

    if ($resultado_editar->num_rows > 0) {
        $usuario_editar = $resultado_editar->fetch_assoc();
    }
}

$sql = "SELECT * FROM usuarios ORDER BY id DESC";

$resultado = $conexao->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Usuários - CadLog</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<div class="dashboard">

    <header>

        <h1>CadLog</h1>

        <div>

            <a href="dashboard.php">
                Dashboard
            </a>

            <a href="logout.php">
                Sair
            </a>

        </div>

    </header>

    <main>

        <h2>Gerenciar usuários</h2>

        <p>
            Usuários cadastrados no sistema.
        </p>

        <?php if ($usuario_editar != null) { ?>

            <div class="info">

                <h3>Editar usuário</h3>

                <form method="POST">

                    <input
                        type="hidden"
                        name="id"
                        value="<?php echo $usuario_editar["id"]; ?>"
                    >

                    <label>Nome</label>

                    <input
                        type="text"
                        name="nome"
                        value="<?php echo htmlspecialchars($usuario_editar["nome"]); ?>"
                        required
                    >

                    <label>E-mail</label>

                    <input
                        type="email"
                        name="email"
                        value="<?php echo htmlspecialchars($usuario_editar["email"]); ?>"
                        required
                    >

                    <label>Nível</label>

                    <?php if ($usuario_editar["id"] == $_SESSION["usuario_id"]) { ?>

                        <input
                            type="text"
                            value="admin"
                            disabled
                        >

                        <input
                            type="hidden"
                            name="nivel"
                            value="admin"
                        >

                    <?php } else { ?>

                        <select name="nivel">

                            <option
                                value="cliente"
                                <?php
                                if ($usuario_editar["nivel"] == "cliente") {
                                    echo "selected";
                                }
                                ?>
                            >
                                Cliente
                            </option>

                            <option
                                value="admin"
                                <?php
                                if ($usuario_editar["nivel"] == "admin") {
                                    echo "selected";
                                }
                                ?>
                            >
                                Administrador
                            </option>

                        </select>

                    <?php } ?>

                    <label>Data de nascimento</label>

                    <input
                        type="date"
                        name="data_nascimento"
                        value="<?php echo $usuario_editar["data_nascimento"]; ?>"
                        required
                    >

                    <button type="submit" name="editar">
                        Salvar alterações
                    </button>

                    <a href="usuarios.php">
                        Cancelar
                    </a>

                </form>

            </div>

        <?php } ?>

        <div class="tabela">

            <table>

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Nível</th>
                        <th>Data de nascimento</th>
                        <th>Ações</th>

                    </tr>

                </thead>

                <tbody>

                <?php while ($usuario = $resultado->fetch_assoc()) { ?>

                    <tr>

                        <td>
                            <?php echo $usuario["id"]; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($usuario["nome"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($usuario["email"]); ?>
                        </td>

                        <td>
                            <?php echo $usuario["nivel"]; ?>
                        </td>

                        <td>
                            <?php
                            echo date(
                                "d/m/Y",
                                strtotime($usuario["data_nascimento"])
                            );
                            ?>
                        </td>

                        <td>

                            <a
                                class="editar"
                                href="usuarios.php?editar=<?php echo $usuario["id"]; ?>"
                            >
                                Editar
                            </a>

                            <?php if ($usuario["id"] != $_SESSION["usuario_id"]) { ?>

                                <?php if ($usuario["nivel"] == "cliente") { ?>

                                    <a
                                        class="editar"
                                        href="usuarios.php?promover=<?php echo $usuario["id"]; ?>"
                                        onclick="return confirm('Deseja promover este usuário para administrador?')"
                                    >
                                        Promover
                                    </a>

                                <?php } ?>

                                <?php if ($usuario["nivel"] == "admin") { ?>

                                    <a
                                        class="editar"
                                        href="usuarios.php?rebaixar=<?php echo $usuario["id"]; ?>"
                                        onclick="return confirm('Deseja rebaixar este administrador para cliente?')"
                                    >
                                        Rebaixar
                                    </a>

                                <?php } ?>

                                <a
                                    class="excluir"
                                    href="usuarios.php?excluir=<?php echo $usuario["id"]; ?>"
                                    onclick="return confirm('Tem certeza que deseja excluir este usuário?')"
                                >
                                    Excluir
                                </a>

                            <?php } else { ?>

                                <span>
                                    Sua conta
                                </span>

                            <?php } ?>

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </main>

</div>

</body>

</html>