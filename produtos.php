<?php

session_start();

include __DIR__ . "/config/conexao.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET["apagar"])) {

    $id = intval($_GET["apagar"]);

    $sql = "DELETE FROM produtos WHERE id = ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: produtos.php");
    exit;
}

if (isset($_POST["acao"]) && $_POST["acao"] == "adicionar") {

    $nome = trim($_POST["nome"]);

    if ($nome != "") {

        $sql = "INSERT INTO produtos (nome) VALUES (?)";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("s", $nome);
        $stmt->execute();
    }

    header("Location: produtos.php");
    exit;
}

if (isset($_POST["acao"]) && $_POST["acao"] == "editar") {

    $id = intval($_POST["id"]);
    $nome = trim($_POST["nome"]);

    if ($nome != "") {

        $sql = "UPDATE produtos SET nome = ? WHERE id = ?";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("si", $nome, $id);
        $stmt->execute();
    }

    header("Location: produtos.php");
    exit;
}

$produto_editar = null;

if (isset($_GET["editar"])) {

    $id = intval($_GET["editar"]);

    $sql = "SELECT * FROM produtos WHERE id = ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $resultado_editar = $stmt->get_result();

    if ($resultado_editar->num_rows > 0) {
        $produto_editar = $resultado_editar->fetch_assoc();
    }
}

$sql = "SELECT * FROM produtos ORDER BY id DESC";

$resultado = $conexao->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Produtos - CadLog</title>

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

        <h2>Tabela de Produtos</h2>

        <p>
            Produtos cadastrados no sistema.
        </p>

        <?php if ($produto_editar != null) { ?>

            <div class="info">

                <h3>Editar Produto</h3>

                <form method="POST">

                    <input
                        type="hidden"
                        name="acao"
                        value="editar"
                    >

                    <input
                        type="hidden"
                        name="id"
                        value="<?php echo $produto_editar["id"]; ?>"
                    >

                    <label>Nome do produto</label>

                    <input
                        type="text"
                        name="nome"
                        value="<?php echo htmlspecialchars($produto_editar["nome"]); ?>"
                        required
                    >

                    <button type="submit">
                        Salvar
                    </button>

                    <a href="produtos.php">
                        Cancelar
                    </a>

                </form>

            </div>

        <?php } ?>

        <?php if (isset($_GET["adicionar"])) { ?>

            <div class="info">

                <h3>Cadastrar Produto</h3>

                <form method="POST">

                    <input
                        type="hidden"
                        name="acao"
                        value="adicionar"
                    >

                    <label>Nome do produto</label>

                    <input
                        type="text"
                        name="nome"
                        required
                    >

                    <button type="submit">
                        Cadastrar
                    </button>

                    <a href="produtos.php">
                        Cancelar
                    </a>

                </form>

            </div>

        <?php } ?>

        <?php if (!isset($_GET["adicionar"]) && $produto_editar == null) { ?>

            <a
                class="editar"
                href="produtos.php?adicionar=1"
            >
                + Adicionar Produto
            </a>

        <?php } ?>

        <div class="tabela">

            <table>

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Produto</th>
                        <th>Ações</th>

                    </tr>

                </thead>

                <tbody>

                <?php while ($produto = $resultado->fetch_assoc()) { ?>

                    <tr>

                        <td>
                            <?php echo $produto["id"]; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($produto["nome"]); ?>
                        </td>

                        <td>

                            <a
                                class="editar"
                                href="produtos.php?editar=<?php echo $produto["id"]; ?>"
                            >
                                Editar
                            </a>

                            <a
                                class="excluir"
                                href="produtos.php?apagar=<?php echo $produto["id"]; ?>"
                                onclick="return confirm('Tem certeza que deseja apagar este produto?')"
                            >
                                Apagar
                            </a>

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