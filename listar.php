<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $pdo = new PDO("sqlite:" . __DIR__ . '/database.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $limit  = isset($_GET['limit'])  ? (int)$_GET['limit']  : 50;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

    $total = $pdo->query("SELECT COUNT(*) FROM leituras")->fetchColumn();

    $stmt = $pdo->prepare("SELECT * FROM leituras ORDER BY criado_em DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $leituras = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "sucesso",
        "total"  => (int)$total,
        "limit"  => $limit,
        "offset" => $offset,
        "dados"  => $leituras
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "erro", "mensagem" => $e->getMessage()]);
}
?>
