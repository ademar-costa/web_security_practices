<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Laboratório XSS</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; }
        .box { border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; background: #f9f9f9; }
        .comment { border-bottom: 1px solid #ddd; padding: 10px 0; }
    </style>
</head>
<body>
    <h1>Livro de Visitas Vulnerável</h1>

    <!-- REFLECTED XSS: Formulário de Busca -->
    <div class="box">
        <h3>Buscar Comentários</h3>
        <form method="GET" action="index.php">
            <input type="text" name="search" placeholder="Digite sua busca...">
            <button type="submit">Buscar</button>
        </form>

        <?php
        // A vulnerabilidade ocorre aqui: o input do usuário é refletido na tela sem nenhuma sanitização
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search_query = $_GET['search'];
            echo "<p>Você buscou por: <strong>" . $search_query . "</strong></p>";
        }
        ?>
    </div>

    <!-- STORED XSS: Formulário de Inserção -->
    <div class="box">
        <h3>Deixe um Comentário</h3>
        <form method="POST" action="index.php">
            <input type="text" name="author" placeholder="Seu Nome" required><br><br>
            <textarea name="message" rows="4" cols="50" placeholder="Sua mensagem..." required></textarea><br><br>
            <button type="submit">Enviar</button>
        </form>

        <?php
        // Inserindo no banco sem Prepared Statements ou escape (Vulnerável também a SQLi, mas o foco é XSS)
        if (isset($_POST['author']) && isset($_POST['message'])) {
            $author = $_POST['author'];
            $message = $_POST['message'];
            $sql = "INSERT INTO comments (author, message) VALUES ('$author', '$message')";
            $conn->query($sql);
        }
        ?>
    </div>

    <!-- STORED XSS: Exibição dos Dados -->
    <div class="box">
        <h3>Mural de Mensagens</h3>
        <?php
        $result = $conn->query("SELECT * FROM comments ORDER BY id DESC");
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // A vulnerabilidade ocorre aqui: os dados do banco são impressos diretamente no HTML
                echo "<div class='comment'>";
                echo "<b>" . $row['author'] . "</b> disse:<br>";
                echo "<p>" . $row['message'] . "</p>";
                echo "</div>";
            }
        } else {
            echo "Nenhum comentário ainda.";
        }
        ?>
    </div>
</body>
</html>