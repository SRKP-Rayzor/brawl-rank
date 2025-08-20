<?php
session_start();
include 'api.php';
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Identifie si on consulte un joueur via recherche
$isFromSearch = isset($_GET['tag']);
$tag = $_GET['tag'] ?? ($_SESSION['tag'] ?? null);

$player = callBrawlAPI(BRAWL_API_URL . "players/" . urlencode("#$tag"));

$logs = callBrawlAPI(BRAWL_API_URL . "players/" . urlencode("#$tag") . "/battlelog");

$brawlers = callBrawlAPI(BRAWL_API_URL . "brawlers");
$events = callBrawlAPI(BRAWL_API_URL . "events/rotation");

$clubTag = $player['club']['tag'] ?? null;
if ($clubTag) {
    $club = callBrawlAPI(BRAWL_API_URL . "clubs/" . urlencode($clubTag));
    $badgeId = $club['badgeId'] ?? null;
}

$nameColor = $player['nameColor'] ?? '#FFFFFF';
$colorCode = '#' . substr($nameColor, 4);

$Trophies = number_format($player['trophies'], 0, '', ' ');
$Record = number_format($player['highestTrophies'], 0, '', ' ');

// Si l’utilisateur est connecté, on récupère son tri perso
$tri = $_GET['tri'] ?? null;

if (!$isFromSearch) {
    $pdo = new PDO("mysql:host=sql100.infinityfree.com;dbname=if0_39688320_test", "if0_39688320", "X1PhomT7ikNALa");

    if ($tri !== null) {
        // Met à jour le tri si on a reçu une nouvelle valeur
        $stmt = $pdo->prepare("UPDATE login SET tri_brawlers = ? WHERE username = ?");
        $stmt->execute([$tri, $_SESSION['username']]);
        $_SESSION['tri_brawlers'] = $tri;
    } else {
        // Sinon, on prend la dernière valeur en base
        $stmt = $pdo->prepare("SELECT tri_brawlers FROM login WHERE username = ?");
        $stmt->execute([$_SESSION['username']]);
        $tri = $stmt->fetchColumn() ?: 'default';
    }
} else {
    // En mode recherche, si aucun tri spécifié → défaut
    $tri = $tri ?? 'default';
}



?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil</title>
  <link rel="shortcut icon" href="BSTracker.ico"/>
  <link rel="apple-touch-icon" href="BSTracker.ico"/>
  <link rel="stylesheet" href="style.css">

  <!-- Déclare que c'est une Web App iOS -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="Brawl Tracker">

<!-- Icône d'app (doit faire au moins 180x180) -->
<link rel="apple-touch-icon" href="/icons/BSTracker.ico">
<link rel="manifest" href="/manifest.json">
<link rel="apple-touch-startup-image" media="screen and (device-width: 440px) and (device-height: 956px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)" href="splash_screens/iPhone_16_Pro_Max_landscape.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 402px) and (device-height: 874px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)" href="splash_screens/iPhone_16_Pro_landscape.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 430px) and (device-height: 932px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)" href="splash_screens/iPhone_16_Plus__iPhone_15_Pro_Max__iPhone_15_Plus__iPhone_14_Pro_Max_landscape.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 393px) and (device-height: 852px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)" href="splash_screens/iPhone_16__iPhone_15_Pro__iPhone_15__iPhone_14_Pro_landscape.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 428px) and (device-height: 926px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)" href="splash_screens/iPhone_14_Plus__iPhone_13_Pro_Max__iPhone_12_Pro_Max_landscape.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 390px) and (device-height: 844px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)" href="splash_screens/iPhone_16e__iPhone_14__iPhone_13_Pro__iPhone_13__iPhone_12_Pro__iPhone_12_landscape.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)" href="splash_screens/iPhone_13_mini__iPhone_12_mini__iPhone_11_Pro__iPhone_XS__iPhone_X_landscape.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)" href="splash_screens/iPhone_11_Pro_Max__iPhone_XS_Max_landscape.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)" href="splash_screens/iPhone_11__iPhone_XR_landscape.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)" href="splash_screens/iPhone_8_Plus__iPhone_7_Plus__iPhone_6s_Plus__iPhone_6_Plus_landscape.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)" href="splash_screens/iPhone_8__iPhone_7__iPhone_6s__iPhone_6__4.7__iPhone_SE_landscape.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)" href="splash_screens/4__iPhone_SE__iPod_touch_5th_generation_and_later_landscape.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 1032px) and (device-height: 1376px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)" href="splash_screens/13__iPad_Pro_M4_landscape.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)" href="splash_screens/12.9__iPad_Pro_landscape.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 834px) and (device-height: 1210px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)" href="splash_screens/11__iPad_Pro_M4_landscape.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 834px) and (device-height: 1194px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)" href="splash_screens/11__iPad_Pro__10.5__iPad_Pro_landscape.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 820px) and (device-height: 1180px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)" href="splash_screens/10.9__iPad_Air_landscape.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)" href="splash_screens/10.5__iPad_Air_landscape.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 810px) and (device-height: 1080px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)" href="splash_screens/10.2__iPad_landscape.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)" href="splash_screens/9.7__iPad_Pro__7.9__iPad_mini__9.7__iPad_Air__9.7__iPad_landscape.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 744px) and (device-height: 1133px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)" href="splash_screens/8.3__iPad_Mini_landscape.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 440px) and (device-height: 956px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)" href="splash_screens/iPhone_16_Pro_Max_portrait.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 402px) and (device-height: 874px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)" href="splash_screens/iPhone_16_Pro_portrait.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 430px) and (device-height: 932px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)" href="splash_screens/iPhone_16_Plus__iPhone_15_Pro_Max__iPhone_15_Plus__iPhone_14_Pro_Max_portrait.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 393px) and (device-height: 852px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)" href="splash_screens/iPhone_16__iPhone_15_Pro__iPhone_15__iPhone_14_Pro_portrait.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 428px) and (device-height: 926px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)" href="splash_screens/iPhone_14_Plus__iPhone_13_Pro_Max__iPhone_12_Pro_Max_portrait.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 390px) and (device-height: 844px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)" href="splash_screens/iPhone_16e__iPhone_14__iPhone_13_Pro__iPhone_13__iPhone_12_Pro__iPhone_12_portrait.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)" href="splash_screens/iPhone_13_mini__iPhone_12_mini__iPhone_11_Pro__iPhone_XS__iPhone_X_portrait.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)" href="splash_screens/iPhone_11_Pro_Max__iPhone_XS_Max_portrait.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)" href="splash_screens/iPhone_11__iPhone_XR_portrait.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)" href="splash_screens/iPhone_8_Plus__iPhone_7_Plus__iPhone_6s_Plus__iPhone_6_Plus_portrait.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)" href="splash_screens/iPhone_8__iPhone_7__iPhone_6s__iPhone_6__4.7__iPhone_SE_portrait.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)" href="splash_screens/4__iPhone_SE__iPod_touch_5th_generation_and_later_portrait.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 1032px) and (device-height: 1376px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)" href="splash_screens/13__iPad_Pro_M4_portrait.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)" href="splash_screens/12.9__iPad_Pro_portrait.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 834px) and (device-height: 1210px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)" href="splash_screens/11__iPad_Pro_M4_portrait.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 834px) and (device-height: 1194px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)" href="splash_screens/11__iPad_Pro__10.5__iPad_Pro_portrait.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 820px) and (device-height: 1180px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)" href="splash_screens/10.9__iPad_Air_portrait.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)" href="splash_screens/10.5__iPad_Air_portrait.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 810px) and (device-height: 1080px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)" href="splash_screens/10.2__iPad_portrait.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)" href="splash_screens/9.7__iPad_Pro__7.9__iPad_mini__9.7__iPad_Air__9.7__iPad_portrait.png">
<link rel="apple-touch-startup-image" media="screen and (device-width: 744px) and (device-height: 1133px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)" href="splash_screens/8.3__iPad_Mini_portrait.png">
</head>
<body style="margin-bottom: 60px;">
<h1>Mon profil</h1>
<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/profile-icons/regular/<?= $player['icon']['id'] ?>.png" style="width: auto; height: 80px; margin-right: 10px;"></img>
<h1 style="color: <?= $colorCode ?>"><?= htmlspecialchars($player['name']) ?></h1>
<h2>(#<?= $tag ?>)</h2>

<?php if (isset($badgeId)): ?>
    <p>
        Club : <a href="recherche.php?c=<?= urlencode(ltrim($club['tag'], '#')) ?>" title="Voir le club"><img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/club-badges/regular/<?= $badgeId ?>.png" alt="Badge" style="width: auto; height: 20px; margin-right: 3px; transform: translateY(+4px)"><?= htmlspecialchars($club['name']) ?></a>
    </p>
<?php endif; ?>
<?php
  if ($Record < 500){
    $trophyRoad = 1;
  } elseif ($Record < 1500){
    $trophyRoad = 2;
  } elseif ($Record < 5000){
    $trophyRoad = 3;
  } elseif ($Record < 10000){
    $trophyRoad = 4;
  } elseif ($Record < 15000){
    $trophyRoad = 5;
  } elseif ($Record < 20000){
    $trophyRoad = 6;
  } elseif ($Record < 25000){
    $trophyRoad = 7;
  } elseif ($Record < 30000){
    $trophyRoad = 8;
  } elseif ($Record < 40000){
    $trophyRoad = 9;
  } elseif ($Record < 50000){
    $trophyRoad = 10;
  } elseif ($Record < 60000){
    $trophyRoad = 11;
  } elseif ($Record < 70000){
    $trophyRoad = 12;
  } elseif ($Record < 80000){
    $trophyRoad = 13;
  } elseif ($Record < 90000){
    $trophyRoad = 14;
  } else {
    $trophyRoad = 15;
  }
?>
<img src="icons/trophy-road/<?= $trophyRoad ?>.png" style="height: 50px;">
<h2>Trophées : <?= $Trophies ?><img src="icons/trophy.png" style="width: auto; height: 25px; margin-left: 3px; transform: translateY(+5px)"></img></h2>
<p>Record de trophées : <?= $Record ?><img src="icons/trophy.png" style="width: auto; height: 20px; margin-left: 3px; transform: translateY(+4px)"></img></p>
<p>Victoires en 3v3: <?= $player['3vs3Victories'] ?><img src="/icons/3v3.png" style="width: auto; height: 18px; margin-left: 3px; transform: translateY(+3px)"></img></p>
<p>Victoires en survivant solo: <?= $player['soloVictories'] ?><img src="icons/solo-showdown.png" style="width: auto; height: 20px; margin-left: 3px; transform: translateY(+5px);"></img></p>
<p>Victoires en survivant duo: <?= $player['duoVictories'] ?><img src="/icons/duo-showdown.png" style="width: auto; height: 25px; margin-left: 3px; transform: translateY(+6px);"></img></p>
<a href="#combats">Voir les derniers combats</a>
<?php include 'navbar.php'; ?>
<h2>Mes brawlers <?= count($player['brawlers']) ?>/<?= count($brawlers['items'])-1 ?></h2>

<form method="GET" style="margin-bottom: 20px;">
  <label for="tri">Trier par :</label>
  <select name="tri" id="tri" onchange="this.form.submit()">
    <option value="default" <?= ($tri ?? '') === 'default' ? 'selected' : '' ?>>Ordre par défaut</option>
    <option value="trophies_max" <?= ($tri ?? '') === 'trophies_max' ? 'selected' : '' ?>>Trophées max.</option>
    <option value="trophies_min" <?= ($tri ?? '') === 'trophies_min' ? 'selected' : '' ?>>Trophées min.</option>
    <option value="name" <?= ($tri ?? '') === 'name' ? 'selected' : '' ?>>Nom</option>
  </select>
</form>

<button onclick="openDefiModal()" style="background-color: rgb(246, 188, 51); padding: 8px; border-radius: 8px; border: none; color: black;">🎯 Générer un défi</button>
<div class="brawler-grid">


<?php

if ($tri !== 'default') {
    usort($player['brawlers'], function ($a, $b) use ($tri) {
        switch ($tri) {
            case 'trophies_max':
                return $b['trophies'] <=> $a['trophies'];
            case 'trophies_min':
                return $a['trophies'] <=> $b['trophies'];
            case 'name':
                return strcmp($a['name'], $b['name']);
            default:
                return 0;
        }
    });
}?>

<?php foreach ($player['brawlers'] ?? [] as $b): ?>
  <?php //vérifie si l'image est disponible localement
$brawlerId = $b['id'];
$localPath = "images/$brawlerId.png"; // chemin relatif à ton script
$cdnUrl = "https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/brawlers/portraits/$brawlerId.png";

// Vérifie si l'image est déjà téléchargée localement
if (file_exists($localPath)) {
    $imgSrc = $localPath;
    echo "<!--local-->";
} else {
    $imgSrc = $cdnUrl;
    echo "<!--url-->";
}
?>
  <div data-id="<?= $b['id'] ?>" class="brawler-card" style="margin-top: 0,2%">



    <div class="img-case-profil" style="position: relative;">
    <img src="<?=$imgSrc?>" alt="<?= htmlspecialchars($b['name']) ?>" style="width: auto; height: 78px; margin: 5px 7px 2px 3px;">
    <div class="gear" style="position: absolute; right: 5px; top: 5px; display: flex; flex-direction: column; gap: 2px;">
    <?php
      if (count($b['gadgets']) >= 1) {
        echo "<img src='icons/gadget.png' style='height: 18px;'>";
      } else {
        echo "<div style='height:18px; width:18px;'></div>"; // espace vide
      }
      if (count($b['gears']) >= 1) {
        echo "<img src='icons/gear.png' style='height: 18px;'>";
      } else {
          echo "<div style='height:18px; width:18px;'></div>"; // espace vide
      }
      if (count($b['starPowers']) >= 1) {
        echo "<img src='icons/starpower.png' style='height: 18px;'>";
      } else {
          echo "<div style='height:18px; width:18px;'></div>"; // espace vide
      }
        if (count($b['gears']) === 2) {
        echo "<img src='icons/gear.png' style='height: 18px;'>";
      } else {
          echo "<div style='height:18px; width:18px;'></div>"; // espace vide
      }
    ?>
    </div>
    <div style="position: absolute; height: 22px; width: 22px; bottom: 0; left: 0; transform: translateY(-3px); background-color: rgb(109, 19, 114); border: solid 4px rgb(233, 74, 206); border-radius: 50px; color: white; font-size: 15px; margin-left: 5px; box-shadow: 0px 2px rgb(0, 0, 0)">
    <strong><p style="transform: translateY(-13px);"><?= $b['power'] ?></p></strong>
    </div>
    </div>
    <span><strong><?= $b['name']?></strong><br><div class="brawler-infos" style="-webkit-text-stroke-width: 0.1px; -webkit-text-stroke-color: #000; -webkit-font-smoothing: antialiased;"><img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/tiers/regular/<?= $b['rank']?>.png" alt="Rang : <?= htmlspecialchars($b['rank'])?>" style="width: auto; height: 30px; transform: translateY(-2px); margin-top: 3px; margin-right: <?php if ( $b['rank'] === 51) { echo "-6"; } else { echo "-1.5"; } ?>px; z-index: 999;"></img><?php
// Exemple de données récupérées via l'API Brawl Stars
$brawlerTrophies = $b['trophies']-($b['rank']*20-20);
$brawlerMaxTrophies = 20;
if ($brawlerTrophies < 0){
   $brawlerTrophies = 0; 
  } elseif ($b['trophies'] >= 1000) {
    $brawlerTrophies = 20;
  }

// Calcul du pourcentage de progression
$percent = min(100, ($brawlerTrophies / $brawlerMaxTrophies) * 100);
?>

  <div class="trophy-bar" style="justify-content: center; transform: translateY(-1px);">
    <div style="position: absolute;"><div style="transform: translateY(-6px) translateX(11px);"><img src="<?php if ( $b['rank'] === 51) { echo "/images/season_trophy.png"; } else { echo "icons/trophy.png"; } ?>" alt="trophés :" style="width: auto; height: 18px; margin-top: 5px; transform: translateY(2px); margin-left: 2px;"></img><strong style="color: <?php if ( $b['rank'] === 51) { echo "white"; } else { echo "rgba(253, 201, 69, 1)"; } ?>; font-size: 20px;"><?= $b['trophies']?></div></strong></div>
    <div class="progress" style="width: <?= round($percent) ?>%;  background: linear-gradient(to bottom, <?php if ($b['rank']=== 51) { echo "rgb(9, 233, 253) 50%, rgb(49, 164, 237) 50%)";} else {echo "rgb(235, 109, 48) 50%, rgb(213, 85, 63) 50%)";} ?>;"></div>
  </div></div></span>
      


    </div>

<?php endforeach; ?>
</div>

<?php

function traduireMode($mode) {
    return match($mode) {
        'trioShowdown' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000009.png"> SURVIVANT TRIO',
        'duoShowdown' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000009.png"> SURVIVANT DUO',
        'soloShowdown' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000006.png"> SURVIVANT SOLO',
        'brawlBall' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000005.png"> BRAWLBALL',
        'knockout' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000020.png"> HORS-JEU',
        'hotZone' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000017.png"> ZONE RÉSERVÉE',
        'gemGrab' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000000.png"> RAZZIA DE GEMMES',
        'bounty' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000003.png"> PRIME',
        'heist' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000002.png"> BRAQUAGE',
        'wipeout' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000025.png"> CHASSE OUVERTE',
        'brawlBall5V5' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000032.png"> BRAWLBALL 5C5',
        'gemGrab5V5' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000033.png"> RAZZIA DE GEMMES 5C5',
        'knockout5V5' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000035.png"> HORS-JEU 5C5',
        'wipeout5V5' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000031.png"> CHASSE OUVERTE 5C5',
        'duels' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000024.png"> DUELS',
        'lastStand' => "<img src='https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000061.png'> CHASSE À L'ONI",
        'hunters' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000028.png"> CHASSEURS',
        'siege' => "<img src='https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000040.png'> VOLEURS D'ÂMES",
        'botDrop' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000062.png"> CHOC DES SAMOURAÏS',
        'showdown+' => '<img src="https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC96WHBzRWNTQUprb0FqRzRldHI2YS5wbmcifQ:supercell:x1MBT3xPMsPzmmyeP_y65TXkCLRnlJtgPq-4zoW0zeg?width=2400"> SURVIVANT+',
        'basketBrawl' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000022.png"> BASKET BRAWL',
        'volleyBrawl' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000023.png"> VOLLEY BRAWL',
        default => strtoupper($mode),
    };
}

function traduireResult($result) {
    return match($result) {
        'victory' => 'VICTOIRE',
        'defeat' => 'DÉFAITE',
        'draw' => 'ÉGALITÉ',
        default => $result,
    };
}

function isShowdownPlus($events, $mapName, $mode) {
    if ($mode !== 'soloShowdown') return false;

    foreach ($events as $event) {
        if (
            isset($event['event']['modifiers']) &&
            in_array('showdown+', $event['event']['modifiers']) &&
            $event['event']['mode'] === 'soloShowdown' &&
            $event['event']['map'] === $mapName
        ) {
            return true;
        }
    }

    return false;
}

$now = new DateTime('now', new DateTimeZone('UTC'));
$upcomingEvents = [];

foreach ($events as $key => $event) {
    $startTimeUTC = DateTime::createFromFormat('Ymd\THis.u\Z', $event['startTime'], new DateTimeZone('UTC'));
    
    if ($startTimeUTC > $now) {
        $upcomingEvents[] = $event;
        unset($events[$key]); // Évite les doublons
    }
}

?>

<h2 id="combats">Derniers combats : </h2>
<div class="battle-logs">
<?php foreach ($logs['items'] as $log): ?>
  <?php
    $mode = $log['event']['mode'] ?? $log['battle']['mode'] ?? 'inconnu';
    $map = $log['event']['map'] ?? 'Carte inconnue';
    $result = $log['battle']['result'] ?? null;
    $trophyChange = $log['battle']['trophyChange'] ?? null ;
    $rank = $log['battle']['rank'] ?? null;
    $type = $log['battle']['type'] ?? null;
    $teams = $log['battle']['teams'] ?? $log['battle']['players'] ?? null;
    $duration = $log['battle']['duration'] ?? null;
    $TimeUTC = DateTime::createFromFormat('Ymd\THis.u\Z', $log['battleTime'], new DateTimeZone('UTC'));
  $combattants = [];

  if (isShowdownPlus($events, $map, $mode)) {
    $mode = 'showdown+';
  }

if (isset($log['battle']['teams'])) {
    // Duo / 3v3 : tableau de tableaux
    $combattants = $log['battle']['teams'];
} elseif (isset($log['battle']['players'])) {
    // Solo → chaque joueur est sa propre équipe (affichage individuel)
    $combattants = array_map(fn($p) => [$p], $log['battle']['players']);
}
  ?>
  <div class="match-card">
    <div class="timer-match" style="">
    <?php if ($type === "challenge"): ?>
        <div class="match-defi" style="text-align: center;">
        <strong>DÉFI SPÉCIAL</strong>
        </div>
    <?php endif; ?>
    <span class="timer" style="font-size: 15px;" data-end="<?= $TimeUTC->format(DateTime::ATOM) ?>"></span>
    </div>
    <div class="match-header">
      <div class="match-mode" style="transform: translateY(-10px);"><?php if ($mode === "unknown" ) { $mode = $log['battle']['mode'];} ?><?= traduireMode($mode) ?></div>
      <?php if ($type === "soloRanked"): ?>
      <div class="match-type" style="transform: translateY(-5px);">
        <img src="https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9QR1hRdXZNeEQ3d2lGa3ZURUM3Mi5wbmcifQ:supercell:bqEfw27fz7e7r8_jO_mhRfIzOCN2H1Xjdl96fiFGBQ4?width=2400" style="height: 18px; transform: translateY(3px);"> CLASSÉ
      </div>
      <?php endif; ?>
      <div class="match-map"><?= htmlspecialchars($map) ?></div>
      <?php if ($result !== null): ?>
        <div class="match-result" style="color: <?= $result === "victory" ? 'rgb(0, 253, 60)' : ($result === "defeat" ? 'red' : 'gray') ?>;"><?= traduireResult($result) ?></div>
      <?php endif; ?>
        <?php if (isset($duration)): ?>
        <div class="match-duration" style="color: gray;">
        Durée : <?= $duration ?>s
        </div>
        <?php endif; ?>
        <div class="match-rank">
        <?= $rank ? "Rang $rank" : '' ?>
        <?php if (($type === "ranked") && ($trophyChange !== null)): ?>
          <span style="color: rgb(253, 198, 69);">
            <?= $trophyChange > 0 ? '+' : '' ?><?= $trophyChange ?>
            <img src="icons/trophy.png" style="height: 16px; transform: translateY(3px);">
          </span>
        <?php endif; ?>
      </div>
    </div>
    <?php if ($combattants): ?>
      <div class="teams">
        <?php foreach ($combattants as $team): ?>
          <div class="team">
            <?php foreach ($team as $joueur): ?>
              <?php if ($mode !== 'duels'): ?>
                <?php
                      $brawlerId = $joueur['brawler']['id'];
                      $localPath = "images/$brawlerId.png"; // chemin relatif à ton script
                      $cdnUrl = "https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/brawlers/portraits/$brawlerId.png";

                      if (file_exists($localPath)) {
                        $imgSrc = $localPath;
                        echo "<!--local-->";
                      } else {
                        $imgSrc = $cdnUrl;
                        echo "<!--url-->";
                      }
                      ?>
                <div class="player" onclick="window.location.href = 'recherche.php?p=<?= urlencode(ltrim($joueur['tag'], '#')) ?>'" style="cursor: pointer;" title="Voir le profil">
                  <div style="position: relative; display: flex; flex-direction: row;">
                    <div class="image-crop">
                      <img src="<?= $imgSrc ?>" style="height: 50px; border: 0.1px solid black; background-color: black; display: block;">
                    </div>
                    <?php if (($type !== "soloRanked") && ($type !== "challenge")): ?>
                      <p style="position: absolute; top: 0; left: 0; transform: translateY(-10px); background-color: black; color: rgba(246, 188, 51, 1); font-size: 10px;"><img src="https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9waFFBanJmTTd0bldQSEFQUTNTTS5wbmcifQ:supercell:uGkNlUITV98HQEBqST8RoyNlyyAA7-NCuOKpR45pPUU?width=2400" style="height: 10px; transform: translateY(2px);"><?= $joueur['brawler']['trophies'] ?></p>
                    <?php endif; ?>
                    <p style="position: absolute; bottom: 0; right: 0; transform: translateY(8px); background-color: black; color: white; font-size: 8px;">NIV. <?= $joueur['brawler']['power'] ?></p>
                  </div>
                  <div class="player-info">
                      <strong><?= htmlspecialchars($joueur['name']) ?></strong><br>
                  </div>
                  </div>
                  <?php else: ?>
                    <div class="duel-players">
                    <div class="duel-player-info">
                      <strong><?= htmlspecialchars($joueur['name']) ?></strong><br>
                    </div>
                    <div class="duel-brawlers" onclick="window.location.href = 'recherche.php?p=<?= urlencode(ltrim($joueur['tag'], '#')) ?>'" style="cursor: pointer;" title="Voir le profil">
                    <?php foreach ($joueur['brawlers'] as $brawlers): ?>
                      <?php
                      $brawlerId = $brawlers['id'];
                      $localPath = "images/$brawlerId.png"; // chemin relatif à ton script
                      $cdnUrl = "https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/brawlers/portraits/$brawlerId.png";

                      if (file_exists($localPath)) {
                        $imgSrc = $localPath;
                        echo "<!--local-->";
                      } else {
                        $imgSrc = $cdnUrl;
                        echo "<!--url-->";
                      }
                      ?>
                      <div style="position: relative; display: flex; flex-direction: row;">
                      <div class="image-crop">
                        <img src="<?= $imgSrc ?>" style="height: 50px; border: 0.1px solid black; background-color: black; display: block;">
                      </div>
                      <p style="position: absolute; top: 0; left: 0; transform: translateY(-10px); background-color: black; color: rgba(246, 188, 51, 1); font-size: 10px;"><img src="https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9waFFBanJmTTd0bldQSEFQUTNTTS5wbmcifQ:supercell:uGkNlUITV98HQEBqST8RoyNlyyAA7-NCuOKpR45pPUU?width=2400" style="height: 10px; transform: translateY(2px);"><?= $brawlers['trophies'] ?></p>
                      <p style="position: absolute; bottom: 0; right: 0; transform: translateY(8px); background-color: black; color: white; font-size: 8px;">NIV. <?= $brawlers['power'] ?></p>
                      </div>
                      <?php endforeach; ?>
                      </div>
                    <div class="duel-trophies">
                      <?php foreach ($joueur['brawlers'] as $brawlers): ?>
                        <?php $trophyChange = $brawlers['trophyChange']; ?>
                          <span style="color: rgb(253, 198, 69);">
                          <?= $trophyChange > 0 ? '+' : '' ?><?= $trophyChange ?>
                          <img src="https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9waFFBanJmTTd0bldQSEFQUTNTTS5wbmcifQ:supercell:uGkNlUITV98HQEBqST8RoyNlyyAA7-NCuOKpR45pPUU?width=2400" style="height: 16px; transform: translateY(3px);">
                        </span>
                      <?php endforeach; ?>
                      </div>
                      </div>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
</div>


<button onclick="topFunction()" id="topBtn" title="Retour en haut">↑</button>
  <script>
    window.onscroll = function() {
      const btn = document.getElementById("topBtn");
      if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
        btn.style.display = "block";
      } else {
        btn.style.display = "none";
      }
    };

    function topFunction() {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  </script>
  <script>
function updateTimers() {
  document.querySelectorAll('.timer').forEach(timer => {
    const endTime = new Date(timer.getAttribute('data-end'));
    const now = new Date();
    const diff = now - endTime;

    if (diff > 0) {
      const totalSeconds = Math.floor(diff / 1000);
      const days = Math.floor(totalSeconds / (3600 * 24));
      const hours = Math.floor((totalSeconds % (3600 * 24)) / 3600);
      const minutes = Math.floor((totalSeconds % 3600) / 60);
      const seconds = totalSeconds % 60;

      const label = "il y a : ";
      const dayStr = days > 0 ? `${days}j ` : ""; // n'affiche rien
      const hourStr = hours > 0 ? `${hours}h ` : ""; // n'affiche rien
      timer.textContent = `${label} ${dayStr} ${hourStr} ${minutes}m ${seconds}s`;
    } else {
      timer.textContent = "Terminé";
    }
  });
}

// Mise à jour toutes les secondes
setInterval(updateTimers, 1000);
updateTimers();
</script>
<div id="loader">
  <div id="chargement"></div>
</div>

<style>
@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
<script>
  // Fonction d'affichage du loader
  function showLoaderAndRedirect(href) {
    const loader = document.getElementById('loader');
    if (!loader) return;
    loader.style.display = 'flex';
    // Donne le temps au navigateur d'afficher le loader
    setTimeout(() => {
      window.location.href = href;
    }, 0);
  }

  // Cible tous les liens de la page
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('a').forEach(link => {
      const href = link.getAttribute('href');
      if (
        !href ||
        href.startsWith('#') ||
        link.hasAttribute('target') ||
        href.startsWith('mailto:') ||
        href.startsWith('javascript:')
      ) return;

      link.addEventListener('click', function (e) {
        e.preventDefault();
        showLoaderAndRedirect(href);
      });
    });
  });


</script>

<div id="defiModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.7); z-index:1000;">
  <div style="background:#1e1e1e; color:white; padding:25px; margin:8% auto; width:90%; max-width:420px; border-radius:12px; box-shadow:0 0 10px #000;">
    <i class="fa-solid fa-xmark fa-xl" onclick="closeDefiModal()" class="btn secondary" style="cursor: pointer;"></i>
    <h2 style="margin-top:0; font-size:22px; text-align:center;">🎯 Génération de défi</h2>

    <label for="difficulty" style="display:block; margin-bottom:5px;">Difficulté :</label>
    <select id="difficulty" style="width:100%; padding:8px; border-radius:6px; border:none; background:#2c2c2c; color:white; margin-bottom:20px;">
      <option value="facile">Facile</option>
      <option value="moyen">Moyen</option>
      <option value="difficile">Difficile</option>
    </select>

    <div class="range-container">
      <label style="display:block; margin-bottom:5px;">Rangs :</label>
      Min :<input type="range" id="rangMin" min="1" max="50" value="1" step="1" oninput="updateRange()">
      Max :<input type="range" id="rangMax" min="1" max="50" value="50" step="1" oninput="updateRange()">
      <div style="text-align:center; margin-top:10px;">
        De <span id="valMin">1</span> à <span id="valMax">50</span>
      </div>
    </div>

    <div style="margin-top:25px; text-align:center;">
      <button onclick="genererDefi()" class="btn primary">🎲 Lancer la roue</button>
    </div>

    <div id="rouletteResult" style="margin-top:20px; font-size:18px; text-align:center;"></div>
  </div>
</div>

<style>
.range-container input[type=range] {
  width: 100%;
  margin: 5px 0;
  -webkit-appearance: none;
  appearance: none;
  height: 6px;
  background: #444;
  border-radius: 3px;
  outline: none;
  padding-left: 3px;
  padding-right: 3px;
}
.range-container input[type=range]::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  height: 18px;
  width: 18px;
  background: #007bff;
  border-radius: 50%;
  cursor: pointer;
  border: 2px solid white;
}
.range-container input[type=range]::-moz-range-thumb {
  height: 18px;
  width: 18px;
  background: #007bff;
  border-radius: 50%;
  cursor: pointer;
  border: 2px solid white;
}

.btn {
  padding: 10px 16px;
  border-radius: 6px;
  font-size: 15px;
  margin: 5px;
  cursor: pointer;
  transition: background 0.2s;
  border: none;
}

.btn.primary {
  background-color: #28a745;
  color: white;
}
.btn.primary:hover {
  background-color: #218838;
}

.btn.secondary {
  background-color: #dc3545;
  color: white;
  cursor: pointer;
}
.btn.secondary:hover {
  background-color: #c82333;
}
</style>

<!-- Fenêtre modale -->
<div id="brawlerModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index: 99999;">
  <div style="background:#222; margin:10% auto; padding:20px; width:80%; max-width:900px; border-radius:10px;">
    <i class="fa-solid fa-xmark fa-xl" onclick="closeModal()" style="cursor:pointer;"></i>
    <div id="brawlerContent">Chargement...</div>
  </div>
</div>

<script>
const brawlers = <?php
  $brawlers = [];
  foreach ($player['brawlers'] as $b) {
    $id = $b["id"];
    $brawlers[] = [
      'name' => $b['name'],
      'rang' => $b['rank'],
      'img' => 'https://github.com/Brawlify/CDN/blob/master/brawlers/borders/'. $id .'.png?raw=true' ?? '' // Remplace par le vrai champ d'image de ton API
    ];
  }
  echo json_encode($brawlers);
?>;

// Précharge toutes les images
const preloadImages = () => {
  for (const brawler of brawlers) {
    if (brawler.img) {
      const img = new Image();
      img.src = brawler.img;
    }
  }
};
preloadImages();

function openDefiModal() {
  document.getElementById('defiModal').style.display = 'block';
}

function closeDefiModal() {
  document.getElementById('defiModal').style.display = 'none';
  document.getElementById('rouletteResult').innerHTML = '';
}

function getProgressionDiff(rang, difficulte) {
  const facteur = { facile: 1, moyen: 2, difficile: 3 };
  const coeff = facteur[difficulte];

  // Plus le rang est haut, plus c’est dur : on réduit la progression possible
  const difficultéRang = Math.max(1, Math.round(coeff * (1 - rang / 60) * 10));
  return Math.min(51 - rang, difficultéRang);
}

function genererDefi() {
  const rangMin = parseInt(document.getElementById('rangMin').value);
  const rangMax = parseInt(document.getElementById('rangMax').value);
  const diff = document.getElementById('difficulty').value;

  const filtrés = brawlers.filter(b => b.rang >= rangMin && b.rang <= rangMax && b.img !== '');

  if (filtrés.length === 0) {
    document.getElementById('rouletteResult').innerHTML = '❌ Aucun brawler trouvé.';
    return;
  }

  let index = 0;
  let tours = 20 + Math.floor(Math.random() * 10);
  const resultDiv = document.getElementById('rouletteResult');

  const spin = setInterval(() => {
    const b = filtrés[index % filtrés.length];
    resultDiv.innerHTML = `
      <div>
        <img src="${b.img}" alt="${b.name}" style="width:80px; height:auto;"><br>
        <span style="font-size:18px;">🎯 ${b.name}</span>
      </div>
    `;
    index++;
if (index > tours) {
  clearInterval(spin);
  const chosen = filtrés[Math.floor(Math.random() * filtrés.length)];
      const progression = getProgressionDiff(chosen.rang, diff);
      const newRang = chosen.rang + progression;
      resultDiv.innerHTML = `
        <div>
          <img src="${chosen.img}" alt="${chosen.name}" style="width:80px; height:auto;"><br>
          <strong style="font-size:20px;">✅ ${chosen.name} : ${chosen.rang} → ${newRang === 51 ? 'MAX' : newRang}</strong>
        </div>
      `;
    }
  }, 100);
}

function updateRange() {
  const minSlider = document.getElementById("rangMin");
  const maxSlider = document.getElementById("rangMax");

  const min = parseInt(minSlider.value);
  const max = parseInt(maxSlider.value);

  // Empêche le croisement
  if (min > max) {
    if (event.target.id === "rangMin") {
      minSlider.value = max;
    } else {
      maxSlider.value = min;
    }
  }

  document.getElementById("valMin").innerText = minSlider.value;
  document.getElementById("valMax").innerText = maxSlider.value;
}

document.addEventListener("DOMContentLoaded", () => {
    // Attache les clics à toutes les cartes brawler
    document.querySelectorAll(".brawler-card").forEach(card => {
        card.addEventListener("click", () => {
            const id = card.dataset.id;
            openbrawlerInfo(id);
        });
    });
});

function openbrawlerInfo(id) {
    fetch("get_brawler.php?id=" + id)
      .then(res => res.text())
      .then(html => {
          document.getElementById("brawlerContent").innerHTML = html;
          document.getElementById("brawlerModal").style.display = "block";
      })
      .catch(() => {
          document.getElementById("brawlerContent").innerHTML = "Erreur de chargement";
          document.getElementById("brawlerModal").style.display = "block";
      });
}

function closeModal() {
    document.getElementById("brawlerModal").style.display = "none";
}

</script>



</body>
</html>