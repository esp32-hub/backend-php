<?php
$pdo = new PDO("sqlite:" . __DIR__ . '/database.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec("CREATE TABLE IF NOT EXISTS leituras (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    temperatura REAL,
    umidade REAL,
    predicao TEXT,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$predicoes = ['normal', 'alerta', 'critico'];
$stmt = $pdo->prepare("INSERT INTO leituras (temperatura, umidade, predicao, criado_em) VALUES (:temp, :umid, :pred, :data)");

for ($i = 0; $i < 80; $i++) {
    $stmt->execute([
        ':temp' => round(20 + lcg_value() * 15, 1),
        ':umid' => round(40 + lcg_value() * 50, 1),
        ':pred' => $predicoes[array_rand($predicoes)],
        ':data' => date('Y-m-d H:i:s', strtotime("-{$i} hours"))
    ]);
}

echo "database.db gerado com 80 leituras de teste.\n";
?>
