<?php

header("Content-Type: application/json; charset=utf-8");

include_once("config.php");

if (isset($_GET["tipo"]) && $_GET["tipo"] === "cadastrar") {

    $nome = $_POST["nome"] ;
    $rm = $_POST["rm"] ;
    $curso = $_POST["curso"] ;
    $nascimento = $_POST["dt_nascimento"] ;

  

    $caminho = null;

    if (isset($_FILES["imagem"])) {
        
        $nomeArquivo = uniqid() . ".jpg";
$pasta= "/uploads/";

move_uploaded_file(
    $_FILES["imagem"]["tmp_name"],
    $pasta . $nomeArquivo
);

$caminho = "uploads/" . $nomeArquivo;
    }

    $sql = "INSERT INTO alunos  (nome, rm, curso, dt_nascimento, imagem) VALUES (?, ?, ?, ?, ?)";

    $enviar = $conexao->prepare($sql);

   

    $enviar->bind_param( "sisss", $nome,$rm,$curso,$nascimento,$caminho );

    if ($enviar->execute()) {

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
