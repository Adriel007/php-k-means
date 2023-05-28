<?php
header('Content-Type: application/json; charset=utf-8');
mb_internal_encoding('UTF-8');
include(dirname(__DIR__) . '/machine-learning/k-means.php');
error_reporting(0);

if (isset($_FILES['file'])) {
    $tmp = $_FILES['file']['tmp_name'];
    $content = file_get_contents($tmp);
    $array = explode('@separatorphp@', $content);
    array_pop($array);

    $jsonResult = json_encode(['result' => markov_chain($array, $level)['markov']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($jsonResult === false)
        echo json_encode(['error' => json_last_error_msg()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    else
        echo $jsonResult;
} else
    echo json_encode(['error' => 'Nenhum arquivo foi enviado'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

function kmeans($texts, $k = 3, $maxIter = 100, $tolerance = 0.0001)
{
    // Cria uma instância da classe Cluster
    $cluster = new KMeans($k, $maxIter, $tolerance);
    // Adiciona os documentos ao cluster
    foreach ($texts as $text) {
        $text = $cluster->clean($text, false);
        $document = array_count_values(str_word_count($text, 1));
        $cluster->addDocument($document);
    }

    // Executa o algoritmo K-means
    $cluster->run();

    // Obtém os clusters resultantes
    $clusters = $cluster->getClusters();

    // Retorna os clusters como array associativo
    $result = [];
    foreach ($clusters as $clusterIndex => $documents)
        $result['Cluster ' . ($clusterIndex + 1)] = $documents;

    return $result;
}