<?php
include 'config.php';
include 'api.php';

$brawlersData = callBrawlAPI(BRAWL_API_URL . "brawlers");

$brawlersList = [];
if (isset($brawlersData['items'])) {
    foreach ($brawlersData['items'] as $brawler) {
        $brawlersList[] = [
            'id' => $brawler['id'],      // ID utilisé pour l'API
            'name' => $brawler['name']   // Nom visible
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($brawlersList);
?>