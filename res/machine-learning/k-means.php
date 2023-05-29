<?php

class KMeans
{
    private $k; // número de clusters
    private $maxIter; // número máximo de iterações
    private $tolerance; // tolerância para convergência
    private $documents; // documentos de entrada
    private $centroids; // centroides dos clusters
    private $clusters; // clusters resultantes

    public function __construct($k = 3, $maxIter = 100, $tolerance = 0.5)
    {
        $this->k = $k;
        $this->maxIter = $maxIter;
        $this->tolerance = $tolerance;
        $this->documents = array();
        $this->centroids = array();
        $this->clusters = array();
    }

    // Adiciona um documento à lista de documentos
    public function addDocument($document)
    {
        $this->documents[] = $document;
    }

    // Inicializa os centroides de forma aleatória
    private function initializeCentroids()
    {
        shuffle($this->documents);
        $this->centroids = array_slice($this->documents, 0, $this->k);
    }

    // Calcula a distância entre dois documentos usando a similaridade de cosseno
    private function cosineSimilarity($doc1, $doc2)
    {
        $dotProduct = 0;
        $normDoc1 = 0;
        $normDoc2 = 0;

        foreach ($doc1 as $term => $weight) {
            $dotProduct += $weight * (isset($doc2[$term]) ? $doc2[$term] : 0);
            $normDoc1 += $weight * $weight;
        }

        foreach ($doc2 as $term => $weight) {
            $normDoc2 += $weight * $weight;
        }

        $similarity = $dotProduct / (sqrt($normDoc1) * sqrt($normDoc2));
        return $similarity;
    }

    // Atribui cada documento ao cluster mais próximo
    private function assignDocumentsToClusters()
    {
        $this->clusters = array();

        foreach ($this->documents as $doc) {
            $bestCluster = 0;
            $bestSimilarity = -1;

            foreach ($this->centroids as $clusterIndex => $centroid) {
                $similarity = $this->cosineSimilarity($doc, $centroid);

                if ($similarity > $bestSimilarity) {
                    $bestCluster = $clusterIndex;
                    $bestSimilarity = $similarity;
                }
            }

            $keys = array_keys($doc);
            $this->clusters[$bestCluster][] = implode(' ', $keys);
        }
    }

    // Atualiza os centroides baseado nos documentos atribuídos a cada cluster
    private function updateCentroids()
    {
        $newCentroids = array();

        foreach ($this->clusters as $cluster) {
            $centroid = array();
            $numDocs = count($cluster);

            foreach ($cluster as $doc) {
                foreach ($doc as $term => $weight) {
                    $centroid[$term] = isset($centroid[$term]) ? $centroid[$term] + $weight : $weight;
                }
            }

            foreach ($centroid as $term => $weight) {
                $centroid[$term] = $weight / $numDocs;
            }

            $newCentroids[] = $centroid;
        }
        $this->centroids = $newCentroids;
    }

    // Executa o algoritmo K-means
    public function run()
    {
        // Inicializa os centroides
        $this->initializeCentroids();

        $numIter = 0;
        $converged = false;

        while ($numIter < $this->maxIter && !$converged) {
            // Atribui os documentos aos clusters
            $this->assignDocumentsToClusters();

            // Atualiza os centroides
            $oldCentroids = $this->centroids;
            $this->updateCentroids();

            // Verifica se houve convergência
            $converged = true;

            foreach ($this->centroids as $clusterIndex => $centroid) {
                $similarity = $this->cosineSimilarity($centroid, $oldCentroids[$clusterIndex]);

                if ($similarity < 1 - $this->tolerance) {
                    $converged = false;
                    break;
                }
            }

            $numIter++;
        }
    }

    public function clean($str)
    {
        $str = strtolower($str);
        $str = strtr($str, ['á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'ä' => 'a', 'Á' => 'A', 'À' => 'A', 'Ã' => 'A', 'Â' => 'A', 'Ä' => 'A', 'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e', 'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i', 'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I', 'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ô' => 'o', 'ö' => 'o', 'Ó' => 'O', 'Ò' => 'O', 'Õ' => 'O', 'Ô' => 'O', 'Ö' => 'O', 'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U', 'ñ' => 'n', 'Ñ' => 'N']);
        return $str;
    }

    // Retorna os clusters resultantes
    public function getClusters()
    {
        return $this->clusters;
    }
}