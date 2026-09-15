<?php
$host="api-cracha-isaquesevero8305-9ba8.f.aivencloud.com";
$usuario="avnadmin";
$bd="defaultdb";
$senha="AVNS_i-c8eSMt_9aE324-P48";
$porta=24413;
$conexao=new mysqli($host,$usuario,$senha,$bd,$porta);

$conexao = mysqli_init();

mysqli_ssl_set(
    $conexao,
    null,
    null,
    __DIR__ . "./ca.pem",
    null,
    null
);

mysqli_real_connect(
    $conexao,
    $host,
    $usuario,
    $senha,
    $bd,
    $porta,
    null,
    MYSQLI_CLIENT_SSL
);

if (!$conexao) {
    die("Erro na conexão: " . mysqli_connect_error());
}

?>
