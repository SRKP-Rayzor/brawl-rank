<?php
include 'config.php';
include 'api.php';


$type   = $_GET['type']   ?? 'players';
$region = $_GET['region'] ?? 'global';
$brawlerId = $_GET['brawler'] ?? '';
$region = strtolower($region);



$apiUrl = BRAWL_API_URL . "rankings/" . $region . "/" . $type;

// Ajouter le paramètre brawler si type = brawlers
if ($type === 'brawlers' && !empty($brawlerId)) {
    $apiUrl .= "/" . $brawlerId;
}

$data = callBrawlAPI($apiUrl);

if (!isset($data['items'])) {
    echo "❌ Erreur de récupération des données";
    exit;
}

echo "<h3>Classement ";
    if ($type === 'brawlers') {
        echo "<img src='images/".$brawlerId.".png' style='height: 50px; border: solid 2px black'>";
    } else {
        echo ucfirst($type);
    }
echo " - " . strtoupper($region) . "</h3><table>";


if ($type === 'players') {
    echo "<div style='padding: 5px; border: solid 1px yellow; background-color: rgba(255, 255, 180, 1); border-radius: 5px; box-shadow: 0px 0px; color: black'>Le nombre de trophées exact des joueurs de plus de 100 000 trophées n'est pas disponible</div>";
    foreach ($data['items'] as $item) {
        echo "<tr onclick=\"showLoaderAndRedirect('recherche.php?p=" . urlencode(ltrim($item['tag'], '#')) . "')\" title='Voir le profil'><td>{$item['rank']}</td><td><div style='display: flex; flex-direction: row;'><img src='https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/profile-icons/regular/". $item['icon']['id'] .".png' style='width: auto; height: 35px; margin-right: 10px; transform: translateY(5px)'></img><div style='display: flex; flex-direction: column; text-align: left;'>{$item['name']}<br><span style='font-size: 13px;'>{$item['club']['name']}</span></div></div></td><td>"; 
        if ($item['trophies'] === 1){ 
            echo "+ 100 000";
        } else { 
            echo $item['trophies'];}
            echo "<img src='https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9waFFBanJmTTd0bldQSEFQUTNTTS5wbmcifQ:supercell:uGkNlUITV98HQEBqST8RoyNlyyAA7-NCuOKpR45pPUU?width=2400' style='height: 20px; transform: translateY(3px);'></td></tr>";
    }
} elseif ($type === 'clubs') {
    foreach ($data['items'] as $item) {
        echo "<tr onclick=\"showLoaderAndRedirect('recherche.php?c=" . urlencode(ltrim($item['tag'], '#')) . "')\" title='Voir le club'><td>{$item['rank']}</td><td><img src='https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/club-badges/regular/". $item['badgeId'] .".png' style='width: auto; height: 30px; margin-right: 7px; transform: translateY(+8px)''>{$item['name']}</td><td>{$item['memberCount']}/30</td><td>{$item['trophies']} <img src='https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9waFFBanJmTTd0bldQSEFQUTNTTS5wbmcifQ:supercell:uGkNlUITV98HQEBqST8RoyNlyyAA7-NCuOKpR45pPUU?width=2400' style='height: 20px; transform: translateY(3px);'></td></tr>";
    }
} elseif ($type === 'brawlers') {
        foreach ($data['items'] as $item) {
        echo "<tr onclick=\"showLoaderAndRedirect('recherche.php?p=" . urlencode(ltrim($item['tag'], '#')) . "')\" title='Voir le profil'><td>{$item['rank']}</td><td><div style='display: flex; flex-direction: row;'><img src='https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/profile-icons/regular/". $item['icon']['id'] .".png' style='width: auto; height: 35px; margin-right: 10px; transform: translateY(5px)'></img><div style='display: flex; flex-direction: column; text-align: left;'>{$item['name']}<br><span style='font-size: 13px;'>{$item['club']['name']}</span></div></div></td><td>{$item['trophies']}<img src='https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9waFFBanJmTTd0bldQSEFQUTNTTS5wbmcifQ:supercell:uGkNlUITV98HQEBqST8RoyNlyyAA7-NCuOKpR45pPUU?width=2400' style='height: 20px; transform: translateY(3px);'></td></tr>";
    }
}


echo "</table>";
?>