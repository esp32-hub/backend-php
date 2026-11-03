<?php
// Define que a resposta será em JSON
header('Content-Type: application/json');

// 1. Pega o corpo da requisição (o JSON enviado pelo ESP32)
$json = file_get_contents('php://input');
$dados = json_decode($json, true);

// Verifica se os dados chegaram corretamente
if (!$dados) {
    http_response_code(400);
    echo json_encode(["status" => "erro", "mensagem" => "JSON inválido ou vazio."]);
    exit;
}

$temperatura = $dados['temperatura'] ?? null;
$umidade     = $dados['umidade'] ?? null;
$predicao    = $dados['predicao'] ?? null;

// 2. Conexão com o Banco de Dados (SQLite com PDO - super simples e sem precisar instalar servidores pesados)
try {
    $dbPath = __DIR__ . '/database.db';
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Cria a tabela automaticamente se ela ainda não existir
    $pdo->exec("CREATE TABLE IF NOT EXISTS leituras (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        temperatura REAL,
        umidade REAL,
        predicao TEXT,
        criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Insere os dados enviados pelo ESP32
    $stmt = $pdo->prepare("INSERT INTO leituras (temperatura, umidade, predicao) VALUES (:temp, :umid, :pred)");
    $stmt->execute([
        ':temp' => $temperatura,
        ':umid' => $umidade,
        ':pred' => $predicao
    ]);

    // Resposta de sucesso para o ESP32
    echo json_encode([
        "status" => "sucesso",
        "mensagem" => "Dados salvos com sucesso no PHP!",
        "recebido" => [
            "temperatura" => $temperatura,
            "umidade" => $umidade,
            "predicao" => $predicao
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "erro",
        "mensagem" => "Erro no banco de dados: " . $e->getMessage()
    ]);
}
?>