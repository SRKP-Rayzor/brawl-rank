<?php
session_start();
include 'api.php';
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
$tag = $_SESSION['tag'] ?? null;
if (!$tag) header("Location: parametres.php?erreur=tag");
$player = callBrawlAPI(BRAWL_API_URL . "players/" . urlencode("#$tag"));

$clubTag = $player['club']['tag'] ?? null;

if ($clubTag) {
    $club = $club ?? callBrawlAPI(BRAWL_API_URL . "clubs/" . urlencode($clubTag));
}

function parseColoredDescription($description) {
    // Match toutes les balises <cX>...</c>
    return preg_replace_callback('/<c(\d)>(.*?)<\/c>/i', function($matches) {
        $colors = [
            '1' => '#000000',
            '2' => '#EE876D',
            '3' => '#AEEC55',
            '4' => '#3964C7',
            '5' => '#83DBEE',
            '6' => '#E779F1',
            '7' => '#F8D36C',
            '8' => '#C473F7',
            '9' => '#EB7376',
        ];
        $code = $matches[1];
        $text = htmlspecialchars($matches[2]); // ici c’est bien
        $color = $colors[$code] ?? '#FFFFFF';
        return "<span style=\"color: $color;\">$text</span>";
    }, $description); // ici on garde le HTML brut
}

function traduireTypeClub($type) {
    return match($type) {
        'open' => 'Ouvert',
        'closed' => 'Fermé',
        'inviteOnly' => 'Sur invitation',
        default => 'Inconnu',
    };
}

$clubTrophies = number_format($club['trophies'], 0, '', ' ');
$requiredTrophies = number_format($club['requiredTrophies'], 0, '', ' ');

$members = $club['members'];


        function tradRole($role) {
    return match($role) {
        'member' => 'Membre',
        'senior' => 'Sénior',
        'vicePresident' => 'Vice-président',
        'president' => 'Président',
        default => 'Inconnu',
    };
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Club</title>
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
<?php if (isset($player['club']['name'])): ?>
    <h1>Mon club</h1>
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
    <tr onclick="window.location.href = 'recherche.php?p=<?= urlencode(ltrim($member['tag'], '#')) ?>'" title="Voir le profil">
        <td><img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/profile-icons/regular/<?=$member['icon']['id']?>.png" style="width: auto; height: 35px; margin-right: 10px; transform: translateY(+2px)"></img></td>
        <td style="min-width: 130px;"><strong style="color: <?= $colorCode ?>; -webkit-text-stroke-width: 0.2px; -webkit-text-stroke-color: #000; -webkit-font-smoothing: antialiased;"><?= htmlspecialchars($member['name']) ?></strong></td>
        <td style="min-width: 76px;"><?= number_format($member['trophies'], 0, '', ' ') ?><img src="https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9waFFBanJmTTd0bldQSEFQUTNTTS5wbmcifQ:supercell:uGkNlUITV98HQEBqST8RoyNlyyAA7-NCuOKpR45pPUU?width=2400" style="width: auto; height: 20px; margin-left: 3px; transform: translateY(+4px)"></img></td>
        <td><?= tradRole($role) ?></td>
    </tr>
<?php endforeach; ?>
</table>


<?php else: ?>
    <h1>Vous n'êtes pas dans un club, rejoignez un club et découvrez ses infos ici !</h1>
    <img src="https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC96UVdTQzlBY0J2UWV6WVV5aEZRYi5naWYifQ:supercell:qTM-Zg83k46TA6feTNSS-S-o3ZkN_cIviyu_FdSEDdc?width=2400" style="">

<?php endif; ?>

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