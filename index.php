<?php

header("Content-Type: application/json; charset=utf-8");

include_once("config.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_GET["tipo"]) && $_GET["tipo"] === "cadastrar") {

    $nome = $_POST["nome"] ?? null;
    $rm = $_POST["rm"] ?? null;
    $curso = $_POST["curso"] ?? null;
    $nascimento = $_POST["dt_nascimento"] ?? null;

    if (!$nome || !$rm || !$curso || !$nascimento) {
        echo json_encode([
            "sucesso" => false,
            "mensagem" => "Dados obrigatórios não recebidos.",
            "dados_recebidos" => [
                "nome" => $nome,
                "rm" => $rm,
                "curso" => $curso,
                "dt_nascimento" => $nascimento
            ]
        ]);
        exit;
    }

    $caminho = null;

    if (isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] === UPLOAD_ERR_OK) {

        $arquivo = $_FILES["imagem"];

        $extensao = pathinfo(
            $arquivo["name"],
            PATHINFO_EXTENSION
        );

        $nomeArquivo = uniqid() . "." . $extensao;

        $pasta = __DIR__ . "/uploads/";

        if (!is_dir($pasta)) {
            mkdir($pasta, 0777, true);
        }

        $destino = $pasta . $nomeArquivo;

        if (!move_uploaded_file(
            $arquivo["tmp_name"],
            $destino
        )) {
            echo json_encode([
                "sucesso" => false,
                "mensagem" => "Não foi possível salvar a imagem."
            ]);
            exit;
        }

        $caminho = "uploads/" . $nomeArquivo;
    }

    $sql = "INSERT INTO alunos 
            (nome, rm, curso, dt_nascimento, imagem)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conexao->prepare($sql);

    if (!$stmt) {
        echo json_encode([
            "sucesso" => false,
            "mensagem" => "Erro ao preparar cadastro.",
            "erro" => $conexao->error
        ]);
        exit;
    }

    $stmt->bind_param(
        "sisss",
        $nome,
        $rm,
        $curso,
        $nascimento,
        $caminho
    );

    if ($stmt->execute()) {

        echo json_encode([
            "sucesso" => true,
            "mensagem" => "Aluno cadastrado com sucesso!",
            "id" => $stmt->insert_id,
            "imagem" => $caminho
        ]);

    } else {

        echo json_encode([
            "sucesso" => false,
            "mensagem" => "Erro ao cadastrar aluno.",
            "erro" => $stmt->error
        ]);
    }

    $stmt->close();
    exit;
}


if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["tipo"]) && $_GET["tipo"] === "buscar") {

    $rm = $_GET["rm"] ?? null;

    if (!$rm) {
        echo json_encode([
            "sucesso" => false,
            "mensagem" => "RM não informado."
        ]);
        exit;
    }

    $stmt = $conexao->prepare(
        "SELECT * FROM alunos WHERE rm = ?"
    );

    $stmt->bind_param("i", $rm);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($aluno = $resultado->fetch_assoc()) {

        echo json_encode([
            "sucesso" => true,
            "aluno" => $aluno
        ]);

    } else {

        echo json_encode([
            "sucesso" => false,
            "mensagem" => "Aluno não encontrado."
        ]);
    }

    $stmt->close();
    exit;
}


echo json_encode([
    "sucesso" => false,
    "mensagem" => "Tipo de requisição inválido."
]);
