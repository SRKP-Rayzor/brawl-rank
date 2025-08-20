<?php
session_start();
include 'api.php';

// ID du brawler cliqué
$id = $_GET['id'] ?? null;

if (!$id || !isset($_SESSION['tag'])) {
    echo "Données manquantes";
    exit;
}

// On récupère les infos du joueur
$player = callBrawlAPI(BRAWL_API_URL . "players/" . urlencode("#" . $_SESSION['tag']));

// On cherche le brawler cliqué dans sa liste
$brawler = null;
foreach ($player['brawlers'] as $b) {
    if ($b['id'] == $id) {
        $brawler = $b;
        break;
    }
}

if (!$brawler) {
    echo "Brawler introuvable.";
    exit;
}

if ( $brawler['rank'] === 51) { $margin1 = "-7"; } else { $margin1 = "-1.5"; }

if ( $b['rank'] === 51) { $trophyImg = "/images/season_trophy.png"; } else { $trophyImg = "icons/trophy.png"; }

if ( $brawler['rank'] === 51) { $color = "white"; } else { $color = "rgba(253, 201, 69, 1)"; }

if ($brawler['rank']=== 51) { $barColor = "rgb(9, 233, 253) 50%, rgb(49, 164, 237) 50%)";} else { $barColor = "rgb(235, 109, 48) 50%, rgb(213, 85, 63) 50%)";}

echo "<h2>" . htmlspecialchars($brawler['name']) . "</h2>";
echo "<div class='brawler-infos' style='-webkit-text-stroke-width: 0.1px; -webkit-text-stroke-color: #000; -webkit-font-smoothing: antialiased; justify-content: left;'><img src='https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/tiers/regular/". $brawler['rank']. ".png' alt='Rang :". $brawler['rank'] ."' style='width: auto; height: 30px; transform: translateY(-2px); margin-top: 3px; margin-right:". $margin1 ."px; z-index: 999;'></img>";


// Exemple de données récupérées via l'API Brawl Stars
$brawlerTrophies = $brawler['trophies']-($brawler['rank']*20-20);
$brawlerMaxTrophies = 20;
if ($brawlerTrophies < 0){
   $brawlerTrophies = 0; 
  } elseif ($brawler['trophies'] >= 1000) {
    $brawlerTrophies = 20;
  }

// Calcul du pourcentage de progression
$percent = min(100, ($brawlerTrophies / $brawlerMaxTrophies) * 100);

if ($brawler['rank'] === 51) {
    $trMax = $brawler['highestTrophies'];
    $trNbSize = 18;
    $margin2 = 17;
} else {
    $trMax = $brawler['rank']*20;
    $trNbSize = 18;
    $margin2 = 10;
}


echo "<div class='trophy-bar' style='justify-content: left; transform: translateY(-2px);'>
    <div style='position: absolute;'><div style='transform: translateY(-7px) translateX(11px);'><img src='". $trophyImg ."' alt='trophés :' style='width: auto; height: 18px; margin-top: 5px; transform: translateY(3px); margin-left: -". $margin2 ."px;'></img><strong style='color:". $color ."; font-size: ". $trNbSize ."px; letter-spacing: -1.5px;'>". $brawler['trophies'] ."/". $trMax ." </div></strong></div>
    <div class='progress' style='width:". round($percent) ."%;  background: linear-gradient(to bottom,". $barColor .";'></div>
  </div></div>";
  
// Affichage HTML (sera injecté dans la modal)
echo "<div style='text-align: center;'><img src='https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/brawlers/model/". $brawler['id'] .".png' style='height: 150px;'></div>";
echo "<p><strong>Niveau de pouvoir :</strong> " . $brawler['power'] . "</p>";

// Gadgets
if (!empty($brawler['gadgets'])) {
    echo "<p><strong>Gadgets :</strong></p><ul>";
    foreach ($brawler['gadgets'] as $g) {
        echo "<li>" . htmlspecialchars($g['name']) . "</li>";
    }
    echo "</ul>";
}

// Star Powers
if (!empty($brawler['starPowers'])) {
    echo "<p><strong>Star Powers :</strong></p><ul>";
    foreach ($brawler['starPowers'] as $sp) {
        echo "<li>" . htmlspecialchars($sp['name']) . "</li>";
    }
    echo "</ul>";
}

// Équipements (gears)
if (!empty($brawler['gears'])) {
    echo "<p><strong>Équipements :</strong></p><ul>";
    foreach ($brawler['gears'] as $gear) {
        echo "<li>" . htmlspecialchars($gear['name']) . " (lvl " . $gear['level'] . ")</li>";
    }
    echo "</ul>";
}
?>