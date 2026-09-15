<?php
header("Content-Type: application/json");

include_once('config.php');

if(isset($_GET['tipo']) && $_GET['tipo']=="buscar"){
    $rm=$_POST['rm'];
 $resultado = $conexao->prepare('Select * from alunos where rm=?');
 $resultado->bind_param('i',$rm);
   
    while ($linha = $resultado->fetch_assoc()) {
        $alunos= $linha;
    }

    echo json_encode([
        "sucesso" => true,
        "alunos" => $alunos
    ]);
}
else if(isset($_GET['tipo']) && $_GET['tipo']=="cadastrar"){
$nome=$_POST['nome'];
$rm=$_POST['rm'];
$curso=$_POST['curso'];
$nascimento=$_POST['dt_nascimento'];
if (isset($_FILES['imagem'])) {
    $arquivo = $_FILES['imagem'];
    $nomearquivo = uniqid() . '.jpg';
    $pasta = 'uploads/';
    if (!is_dir($pasta)) {
        mkdir($pasta, 0777, true);
    }
    move_uploaded_file(
        $arquivo['tmp_name'],
        $pasta . $nomearquivo
    );
    $caminho=$pasta+$nomeArquivo;
}
$enviar=$conexao->prepare('INSERT INTO alunos (nome, rm, curso, dt_nascimento, imagem) VALUES (?, ?, ?, ?, ?)');

$enviar->bind_param( 'sisss', $nome,$rm,$curso,$nascimento,$caminho);

    if ($enviar->execute()) {

        echo json_encode([
            "sucesso" => true,
            "mensagem" => "Aluno cadastrado com sucesso"
        ]);
}
}
?>