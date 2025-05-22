<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criptografia Avançada</title>
    <link href="media/css/estilos.css" rel="stylesheet" type="text/css">
</head>
<body>
    <h2>Criptografia Avançada</h2>

    <!-- Formulário com campo de texto e seletor para criptografar ou descriptografar -->
    <form method="POST">
        <input type="text" name="mensagem" placeholder="Digite a mensagem" required>
        <select name="acao">
            <option value="criptografar">Criptografar</option>
            <option value="descriptografar">Descriptografar</option>
        </select>
        <button type="submit">Enviar</button>
    </form>

<?php
// Definição da chave secreta usada na criptografia
// A chave precisa ter 32 bytes para o AES-256 funcionar corretamente
define('CHAVE_SECRETA', 'minha_chave_super_secreta_123456'); // (32 caracteres)

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mensagem = $_POST['mensagem']; // Mensagem recebida do formulário
    $acao = $_POST['acao'];         // Ação escolhida: criptografar ou descriptografar

    // Realiza a ação escolhida e exibe o resultado
    if ($acao == "criptografar") {
        $mensagem_criptografada = criptografar($mensagem);
        echo "<p><strong>Mensagem Criptografada:</strong> " . htmlspecialchars($mensagem_criptografada) . "</p>";
    } elseif ($acao == "descriptografar") {
        $mensagem_descriptografada = descriptografar($mensagem);
        echo "<p><strong>Mensagem Descriptografada:</strong> " . htmlspecialchars($mensagem_descriptografada) . "</p>";
    }
}

// Função para criptografar uma mensagem com AES-256-CBC e IV aleatório
function criptografar($mensagem) {
    $iv = openssl_random_pseudo_bytes(16); // Gera IV aleatório de 16 bytes (necessário para AES-CBC)

    // Criptografa a mensagem usando AES-256-CBC
    $criptografada = openssl_encrypt(
        $mensagem,
        'aes-256-cbc',
        CHAVE_SECRETA,
        OPENSSL_RAW_DATA, // Retorna dados binários
        $iv
    );

    // Concatena o IV com o texto criptografado e codifica em base64 para facilitar o envio
    return base64_encode($iv . $criptografada);
}

// Função para descriptografar a mensagem recebida
function descriptografar($mensagem) {
    // Decodifica a string base64
    $dados = base64_decode($mensagem, true);

    // Verifica se a string é válida e tem pelo menos 17 bytes (16 do IV + algo criptografado)
    if ($dados === false || strlen($dados) < 17) {
        return "Erro: Mensagem inválida.";
    }

    // Extrai os 16 primeiros bytes como IV e o restante como a mensagem criptografada
    $iv = substr($dados, 0, 16);
    $conteudo = substr($dados, 16);

    // Descriptografa usando o mesmo algoritmo, chave e IV
    $descriptografada = openssl_decrypt(
        $conteudo,
        'aes-256-cbc',
        CHAVE_SECRETA,
        OPENSSL_RAW_DATA,
        $iv
    );

    // Verifica se a descriptografia foi bem-sucedida
    if ($descriptografada === false) {
        return "Erro: Não foi possível descriptografar.";
    }

    return $descriptografada;
}
?>
</body>
</html>
