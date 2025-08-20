<?php
session_start();
include 'api.php';


$erreurRecherche = null;

if (isset($_GET['p'])) {
    $tag = strtoupper($_GET['p']);
    $joueur = callBrawlAPI(BRAWL_API_URL . "players/" . urlencode("#" . $tag));

    if (!isset($joueur['tag'])) {
        $erreurRecherche = "Aucun joueur trouvé pour le tag « #" . htmlspecialchars($tag) . " ».";
    }
}

if (isset($_GET['c'])) {
    $tag = strtoupper($_GET['c']);
    $club = callBrawlAPI(BRAWL_API_URL . "clubs/" . urlencode("#" . $tag));

    if (!isset($club['tag'])) {
        $erreurRecherche = "Aucun club trouvé pour le tag « #" . htmlspecialchars($tag) . " ».";
    }
}

// --- Détection du type de recherche ---
if (isset($_GET['p'])) {
    // Recherche d’un joueur
    $tag = $_GET['p'];
    $player = callBrawlAPI(BRAWL_API_URL . "players/" . urlencode("#$tag"));
    $logs = callBrawlAPI(BRAWL_API_URL . "players/" . urlencode("#$tag") . "/battlelog");

    $clubTag = $player['club']['tag'] ?? null;
    if ($clubTag) {
        $club = callBrawlAPI(BRAWL_API_URL . "clubs/" . urlencode($clubTag));
        $badgeId = $club['badgeId'] ?? null;
    }

    $nameColor = $player['nameColor'] ?? '#FFFFFF';
    $colorCode = '#' . substr($nameColor, 4);

    $Trophies = number_format($player['trophies'], 0, '', ' ');
    $Record = number_format($player['highestTrophies'], 0, '', ' ');

    $tri = $_GET['tri'] ?? 'default';

    if ($tri !== 'default') {
        usort($player['brawlers'], function ($a, $b) use ($tri) {
            return match($tri) {
                'trophies_max' => $b['trophies'] <=> $a['trophies'],
                'trophies_min' => $a['trophies'] <=> $b['trophies'],
                'name' => strcmp($a['name'], $b['name']),
                default => 0
            };
        });
    }

} elseif (isset($_GET['c'])) {
    // Recherche d’un club
    $clubTag = $_GET['c'];
    $club = callBrawlAPI(BRAWL_API_URL . "clubs/" . urlencode("#$clubTag"));
    $sessionTag = $_SESSION['tag'];
    $playerClub = callBrawlAPI(BRAWL_API_URL . "players/" . urlencode("#$sessionTag"));
    $members = $club['members'];

    $clubTrophies = number_format($club['trophies'], 0, '', ' ');
    $requiredTrophies = number_format($club['requiredTrophies'], 0, '', ' ');

    function parseColoredDescription($description) {
        return preg_replace_callback('/<c(\d)>(.*?)<\/c>/', function($matches) {
            $colors = [
                '1' => '#000000', '2' => '#EE876D', '3' => '#AEEC55', '4' => '#3964C7',
                '5' => '#83DBEE', '6' => '#E779F1', '7' => '#F8D36C', '8' => '#C473F7', '9' => '#EB7376',
            ];
            $color = $colors[$matches[1]] ?? '#FFFFFF';
            return "<span style=\"color: $color;\">".htmlspecialchars($matches[2])."</span>";
        }, $description);
    }

    function traduireTypeClub($type) {
        return match($type) {
            'open' => 'Ouvert',
            'closed' => 'Fermé',
            'inviteOnly' => 'Sur invitation',
            default => 'Inconnu',
        };
    }

    function tradRole($role) {
        return match($role) {
            'member' => 'Membre',
            'senior' => 'Sénior',
            'vicePresident' => 'Vice-président',
            'president' => 'Président',
            default => 'Inconnu',
        };
    }

}

// --- Détection du type de recherche ---
if (isset($_GET['p'])) {
    $type = 'p'; // joueur
} elseif (isset($_GET['c'])) {
    $type = 'c'; // club
}

if (isset($_SESSION['username'])) {
    // Tag joueur connecté
    $sessionPlayerTag = strtoupper(ltrim($_SESSION['tag'], '#'));
    $sessionPlayer = callBrawlAPI(BRAWL_API_URL . "players/" . urlencode("#" . $sessionPlayerTag));
    $sessionClubTag = isset($sessionPlayer['club']['tag']) ? strtoupper(ltrim($sessionPlayer['club']['tag'], '#')) : null;

    if ($type === 'p' && $sessionPlayerTag === strtoupper(ltrim($tag, '#'))) {
        // Recherche de soi-même -> profil
        header("Location: profil.php");
        exit;
    }

    if ($type === 'c' && $sessionClubTag && $sessionClubTag === strtoupper(ltrim($tag, '#'))) {
        // Recherche du club de l'utilisateur -> page club
        header("Location: club.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recherche</title>
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
    <div class="connect">
<form id="searchForm" style="margin-bottom: 20px;">
    <input type="text" id="tagInput" placeholder="Tag du joueur ou du club" value="#<?= $tag ?>" required>
    <select id="typeSelect">
        <option value="p" <?= ($type ?? '') === 'p' ? 'selected' : '' ?>>Joueur</option>
        <option value="c" <?= ($type ?? '') === 'c' ? 'selected' : '' ?>>Club</option>
    </select>
    <button type="submit">Rechercher</button>
</form>

<?php if (isset($erreurRecherche)): ?>
    <div style="background-color: #ffdddd; border: 1px solid #ff5c5c; color: #900; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
        <?= htmlspecialchars($erreurRecherche) ?>
    </div>
    <img src="https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9xYXdtNTJBeVVZY2FLSzdkem41Si5naWYifQ:supercell:BD7UU0iGgJiXgk3c0ZGkgxSHYan-pJvm7bSi8mz5L1I?width=2400" style="height: 300px; weigth: auto;">
<?php endif; ?>
<script>
  document.getElementById("searchForm").addEventListener("submit", function(e) {
      e.preventDefault(); // Empêche l’envoi classique
      const tag = document.getElementById("tagInput").value.trim().replace(/^#/, '');
      const type = document.getElementById("typeSelect").value;
      if (tag !== '') {
          window.location.href = `recherche.php?${type}=${encodeURIComponent(tag)}`;
      }
  });
</script>
    </div>

<?php if (!$erreurRecherche): ?>
    <?php if (isset($player)): ?>
<!-- Affichage du profil joueur -->

<h1>Profil du joueur</h1>
<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/profile-icons/regular/<?= $player['icon']['id'] ?>.png" style="width: auto; height: 80px; margin-right: 10px;"></img>
<h1 style="color: <?= $colorCode ?>"><?= htmlspecialchars($player['name']) ?></h1>
<h2>(#<?= $tag ?>)</h2>

<?php if (isset($badgeId)): ?>
    <p>
        Club : <a href="recherche.php?c=<?= urlencode(ltrim($club['tag'], '#')) ?>" title="Voir le club"><img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/club-badges/regular/<?= $badgeId ?>.png" alt="Badge" style="width: auto; height: 20px; margin-right: 3px; transform: translateY(+4px)"><?= htmlspecialchars($club['name']) ?></a>
    </p>
<?php endif; ?>

<h2>Trophées : <?= $Trophies ?><img src="https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9waFFBanJmTTd0bldQSEFQUTNTTS5wbmcifQ:supercell:uGkNlUITV98HQEBqST8RoyNlyyAA7-NCuOKpR45pPUU?width=2400" style="width: auto; height: 20px; margin-left: 3px; transform: translateY(+4px)"></img></h2>
<p>Record de trophées : <?= $Record ?><img src="https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9waFFBanJmTTd0bldQSEFQUTNTTS5wbmcifQ:supercell:uGkNlUITV98HQEBqST8RoyNlyyAA7-NCuOKpR45pPUU?width=2400" style="width: auto; height: 20px; margin-left: 3px; transform: translateY(+4px)"></img></p>
<p>Victoires en 3v3: <?= $player['3vs3Victories'] ?><img src="https://cdn-misc.brawlify.com/icon/3v3.png" style="width: auto; height: 18px; margin-left: 3px; transform: translateY(+3px)"></img></p>
<p>Victoires en survivant solo: <?= $player['soloVictories'] ?><img src="https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9nbkxKdVIxRHlZTE1qUlMzV2pMTC5wbmcifQ:supercell:OfOmUHA0JeavDf4uX8SuyvQBmOq0AwN8aKuXPiM946Q?width=2400" style="width: auto; height: 20px; margin-left: 3px; transform: translateY(+5px);"></img></p>
<p>Victoires en survivant duo: <?= $player['duoVictories'] ?><img src="https://cdn-misc.brawlify.com/gamemode/Duo-Showdown.png" style="width: auto; height: 25px; margin-left: 3px; transform: translateY(+6px);"></img></p>

<h2>Ses brawlers</h2>
<form method="GET">
  <input type="hidden" name="p" value="<?= htmlspecialchars($tag) ?>">
  <label for="tri">Trier :</label>
  <select name="tri" onchange="this.form.submit()">
    <option value="default" <?= ($tri ?? '') === 'default' ? 'selected' : '' ?>>Par défaut</option>
    <option value="trophies_max" <?= ($tri ?? '') === 'trophies_max' ? 'selected' : '' ?>>Trophées max</option>
    <option value="trophies_min" <?= ($tri ?? '') === 'trophies_min' ? 'selected' : '' ?>>Trophées min</option>
    <option value="name" <?= ($tri ?? '') === 'name' ? 'selected' : '' ?>>Nom</option>
  </select>
</form>
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
  <div class="brawler-card" style="margin-top: 0,2%">
    <div class="brawler-power" style="position: relative">
    <img src="<?=$imgSrc?>" alt="<?= htmlspecialchars($b['name']) ?>" style="width: auto; height: 78px; margin: 5px 7px 2px 3px;"><br>
    <div style="position: absolute; height: 22px; width: 22px; bottom: 0; right: 0; transform: translateY(-5px); background-color: rgb(109, 19, 114); border: solid 4px rgb(233, 74, 206); border-radius: 50px; color: white; font-size: 15px; margin-right: 5px; box-shadow: 0px 2px rgb(0, 0, 0)">
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
    <div style="position: absolute;"><div style="transform: translateY(-6px) translateX(11px);"><img src="<?php if ( $b['rank'] === 51) { echo "/images/season_trophy.png"; } else { echo "https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9waFFBanJmTTd0bldQSEFQUTNTTS5wbmcifQ:supercell:uGkNlUITV98HQEBqST8RoyNlyyAA7-NCuOKpR45pPUU?width=2400"; } ?>" alt="trophés :" style="width: auto; height: 18px; margin-top: 5px; transform: translateY(2px); margin-left: 2px;"></img><strong style="color: <?php if ( $b['rank'] === 51) { echo "white"; } else { echo "rgba(253, 201, 69, 1)"; } ?>; font-size: 20px;"><?= $b['trophies']?></div></strong></div>
    <div class="progress" style="width: <?= round($percent) ?>%;  background: linear-gradient(to bottom, <?php if ($b['rank']=== 51) { echo "rgb(9, 233, 253) 50%, rgb(49, 164, 237) 50%)";} else {echo "rgb(235, 109, 48) 50%, rgb(213, 85, 63) 50%)";} ?>;"></div>
  </div></div></span>
      


    </div>

<?php endforeach; ?>
</div>

<h2>Derniers combats</h2>
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
?>
<h2 id="combats">Derniers combats : </h2>
<div class="battle-logs">
<?php foreach ($logs['items'] as $log): ?>
  <?php
    $mode = $log['event']['mode'] ?? $log['battle']['mode'] ?? 'inconnu';
    $map = $log['event']['map'] ?? 'Carte inconnue';
    $result = $log['battle']['result'] ?? null;
    $trophyChange = $log['battle']['trophyChange'] ?? null;
    $rank = $log['battle']['rank'] ?? null;
    $type = $log['battle']['type'] ?? null;
    $teams = $log['battle']['teams'] ?? $log['battle']['players'] ?? null;
$combattants = [];

if (isset($log['battle']['teams'])) {
    // Duo / 3v3 : tableau de tableaux
    $combattants = $log['battle']['teams'];
} elseif (isset($log['battle']['players'])) {
    // Solo → chaque joueur est sa propre équipe (affichage individuel)
    $combattants = array_map(fn($p) => [$p], $log['battle']['players']);
}
  ?>
  <div class="match-card">
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
        <div class="match-rank">
        <?= $rank ? "Rang $rank" : '' ?>
        <?php if ($trophyChange !== null): ?>
          <span style="color: rgb(253, 198, 69);">
            <?= $trophyChange > 0 ? '+' : '' ?><?= $trophyChange ?>
            <img src="https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9waFFBanJmTTd0bldQSEFQUTNTTS5wbmcifQ:supercell:uGkNlUITV98HQEBqST8RoyNlyyAA7-NCuOKpR45pPUU?width=2400" style="height: 16px; transform: translateY(3px);">
          </span>
        <?php endif; ?>
      </div>
    </div>
    <?php if ($combattants): ?>
      <div class="teams">
        <?php foreach ($combattants as $team): ?>
          <div class="team">
            <?php foreach ($team as $joueur): ?>
              <div class="player" onclick="window.location.href = 'recherche.php?p=<?= urlencode(ltrim($joueur['tag'], '#')) ?>'" style="cursor: pointer;" title="Voir le profil">
                <div style="position: relative;">
                <div class="image-crop">
                <img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/brawlers/portraits/<?= $joueur['brawler']['id'] ?>.png" style="height: 50px; border: 0.1px solid black; background-color: black;">
                </div>
                <?php if ($type !== "soloRanked"): ?>
                <p style="position: absolute; top: 0; left: 0; transform: translateY(-10px); background-color: black; color: rgba(246, 188, 51, 1); font-size: 10px;"><img src="https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9waFFBanJmTTd0bldQSEFQUTNTTS5wbmcifQ:supercell:uGkNlUITV98HQEBqST8RoyNlyyAA7-NCuOKpR45pPUU?width=2400" style="height: 10px; transform: translateY(2px);"><?= $joueur['brawler']['trophies'] ?></p>
                <?php endif; ?>
                  <p style="position: absolute; bottom: 0; right: 0; transform: translateY(4px); background-color: black; color: white; font-size: 8px;">NIV. <?= $joueur['brawler']['power'] ?></p>
                </img>
                </div>
                <div class="player-info">
                  <strong><?= htmlspecialchars($joueur['name']) ?></strong><br>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
</div>

<?php elseif (isset($club)): ?>
<!-- Affichage du club -->
   <?php 
$testTag = strtoupper(ltrim($playerClub['club']['tag'], '#'));
$searchedTag = strtoupper(ltrim($clubTag, '#'));

if (isset($_SESSION['username']) && $searchedTag === $testTag) {
    header("Location: club.php");
    exit;
}
 ?>

<h1><?= $club['name'] ?><img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/club-badges/regular/<?= $club['badgeId'] ?>.png" style="width: auto; height: 40px; margin-left: 8px; transform: translateY(+10px)"></h1>
    <h2><?= $club['tag'] ?></h2>
    <p>Description : <?= parseColoredDescription($club['description']) ?></p>
    <p>Type : <?= traduireTypeClub($club['type']) ?></p>
    <p>Trophés requis : <?= $requiredTrophies ?><img src="https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9waFFBanJmTTd0bldQSEFQUTNTTS5wbmcifQ:supercell:uGkNlUITV98HQEBqST8RoyNlyyAA7-NCuOKpR45pPUU?width=2400" style="width: auto; height: 20px; margin-left: 3px; transform: translateY(+4px)"></img></p>
    <h2>Trophés : <?= $clubTrophies ?> <img src="https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9waFFBanJmTTd0bldQSEFQUTNTTS5wbmcifQ:supercell:uGkNlUITV98HQEBqST8RoyNlyyAA7-NCuOKpR45pPUU?width=2400" style="width: auto; height: 30px; margin-left: 0px; transform: translateY(+6px)"></img></h2>

<h2>Membres du club <?= count($members) ?>/30</h2>
<p>Cliquez sur un joueur pour voir le profil</p>
<table>
<?php foreach ($members as $member): ?>
    <?php
        $nameColor = $member['nameColor'] ?? '#FFFFFF'; // fallback blanc
        $colorCode = '#' . substr($nameColor, 4);

        $role = $member['role'];


    ?>
    <tr onclick="window.location.href = 'recherche.php?p=<?= urlencode(ltrim($member['tag'], '#')) ?>'">
        <td><img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/profile-icons/regular/<?=$member['icon']['id']?>.png" style="width: auto; height: 35px; margin-right: 10px; transform: translateY(+2px)"></img></td>
        <td style="min-width: 130px;"><strong style="color: <?= $colorCode ?>; -webkit-text-stroke-width: 0.2px; -webkit-text-stroke-color: #000; -webkit-font-smoothing: antialiased;"><?= htmlspecialchars($member['name']) ?></strong></td>
        <td style="min-width: 76px;"><?= number_format($member['trophies'], 0, '', ' ') ?><img src="https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9waFFBanJmTTd0bldQSEFQUTNTTS5wbmcifQ:supercell:uGkNlUITV98HQEBqST8RoyNlyyAA7-NCuOKpR45pPUU?width=2400" style="width: auto; height: 20px; margin-left: 3px; transform: translateY(+4px)"></img></td>
        <td><?= tradRole($role) ?></td>
    </tr>
<?php endforeach; ?>
</table>
<?php endif; // fin du isset($club) ?>
<?php endif; // fin du !erreurRecherche ?>
<div class="connect">
<img src="https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9jejNudk5nZ0R5cXludEs2U3gzYi5naWYifQ:supercell:wwleDxtumi_-Dfn20RI51pbIQ6AzJRMSASDCoJQiSEg?width=2400" style="height: 300px; weigth: auto;">
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
<div id="loader">
  <div id="chargement"></div>
</div>

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
<?php include 'navbar.php'; ?>

</body>
</html>