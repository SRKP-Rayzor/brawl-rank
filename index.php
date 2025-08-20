<?php session_start(); 
include 'api.php';
$events = callBrawlAPI(BRAWL_API_URL . "events/rotation");
$players = callBrawlAPI(BRAWL_API_URL . "rankings/fr/players");


function traduireMode($mode) {
    return match($mode) {
        'trioShowdown' => 'SURVIVANT TRIO',
        'duoShowdown' => 'SURVIVANT DUO',
        'soloShowdown' => 'SURVIVANT SOLO',
        'brawlBall' => 'BRAWLBALL',
        'knockout' => 'HORS-JEU',
        'hotZone' => 'ZONE RÉSERVÉE',
        'gemGrab' => 'RAZZIA DE GEMMES',
        'bounty' => 'PRIME',
        'heist' => 'BRAQUAGE',
        'wipeout' => 'CHASSE OUVERTE',
        'brawlBall5V5' => 'BRAWLBALL 5C5',
        'gemGrab5V5' => 'RAZZIA DE GEMMES 5C5',
        'knockout5V5' => 'HORS-JEU 5C5',
        'wipeout5V5' => 'CHASSE OUVERTE 5C5',
        'duels' => 'DUELS',
        'lastStand' => "CHASSE À L'ONI",
        'hunters' => 'CHASSEURS',
        'showdown+' => 'SURVIVANT+',
        'volleyBrawl' => 'VOLLEY BRAWL',
        'basketBrawl' => 'BASKET BRAWL',
        'unknown' => 'ÉVÉNEMENT INCONNU',
        'trophyThieves' => 'VOL DE TROPHÉES',
        'bossFight' => 'COMBAT DE BOSS',
        default => strtoupper($mode),
    };
}

function traduireImage($mode) {
    return match($mode) {
        'trioShowdown' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000009.png">',
        'duoShowdown' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000009.png">',
        'soloShowdown', 'showdown+' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000006.png">',
        'brawlBall' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000005.png">',
        'knockout' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000020.png">',
        'hotZone' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000017.png">',
        'gemGrab' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000000.png">',
        'bounty' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000003.png">',
        'heist' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000002.png">',
        'wipeout' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000025.png">',
        'brawlBall5V5' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000032.png">',
        'gemGrab5V5' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000033.png">',
        'knockout5V5' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000035.png">',
        'wipeout5V5' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000031.png">',
        'duels' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000024.png">',
        'lastStand' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000061.png">',
        'hunters' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000028.png">',
        'volleyBrawl' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000023.png">',
        'basketBrawl' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000022.png">',
        'trophyThieves' => '<img src="https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9zMndqZmNmVGdRS295WXVFYTM4OC5wbmcifQ:supercell:6LdO0mptnliXOQStVJDNAbi9EdkouwdXWz9xBu_t4zs">',
        'bossFight' => '<img src="https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/game-modes/regular/48000010.png">',
        default => '',
    };
}

function getEventColor($mode) {
    return match($mode) {
        'soloShowdown', 'duoShowdown', 'trioShowdown' => 'background-color: rgb(113, 207, 58)',
        'gemGrab', 'gemGrab5V5'  => 'background-color: rgb(149, 56, 232)',
        'heist' => 'background-color: rgb(210, 85, 200)',
        'brawlBall', 'brawlBall5V5' => 'background-color: rgb(131, 149, 214)',
        'knockout', 'knockout5V5' => 'background-color: rgb(244, 124, 45)',
        'bounty' => 'background-color: rgb(23, 199, 251)',
        'hotZone' => 'background-color: rgb(223, 58, 73)',
        'wipeout', 'wipeout5V5' => 'background-color: rgb(233, 59, 180)',
        'duels' => 'background: linear-gradient(96deg, rgb(83, 146, 247) 11.9%, rgb(198, 51, 32) 12.1%)',
        'hunters' => 'background-color: rgb(234, 59, 87)',
        'showdown+' => 'background: linear-gradient(to bottom, rgb(149, 212, 73) 37%, rgb(216, 153, 57) 43%);',
        'volleyBrawl' => 'background-color: rgb(214, 250, 72)',
        'basketBrawl' => 'background-color: rgb(98, 193, 244)',
        'trophyThieves' => 'background-color: rgb(236, 98, 42)',
        'bossFight' => 'background-color: rgb(218, 52, 39)',
        default => 'background-color: #999999ff',                                           // Gris par défaut
    };
}

function traduireBanner($mode) {
    return match($mode) {
        'soloShowdown', 'duoShowdown', 'trioShowdown' => 'https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9qb3BkYUR0dnBoZFQ1RGg4M3d1US5wbmcifQ:supercell:OvzsSiT5zF6bhju4mhKaGhcRLzQ-BKHf3Aj1W5YxtyU?width=2400',
        'brawlBall', 'brawlBall5V5' => 'https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC94Zk1CaExCb3VHNWhicDRxclZKRC5wbmcifQ:supercell:l_ZwgSBF2ClYbOpTaSMhBvtTUqTBv-R6wakNNIyvCYA?width=2400',
        'knockout', 'knockout5V5' => 'https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9MWVRFaWpHRXd0WVQ2UXpUelMxNC5wbmcifQ:supercell:AzQrPI9WkFdSdlvYA0Cz9PqD_V5Bw_gMtIU1qhAyNOM?width=2400',
        'hotZone' => 'https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9nN2p3Q2RWaTYzQjFVTHVHdWtQUy5wbmcifQ:supercell:uwM_vqXe-_2agNRxOI0-RjcqqVOpk_YLoz7l1O4SoJc?width=2400',
        'gemGrab', 'gemGrab5V5' => 'https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9XVjZZOTdlNWdFZkdrOFdCaDh6Ni5wbmcifQ:supercell:R1xup1xCwuzTHhMncH5PV8fdt9ZXNoiBqsjEUh4BML4?width=2400',
        'bounty' => 'https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9nN2p3Q2RWaTYzQjFVTHVHdWtQUy5wbmcifQ:supercell:uwM_vqXe-_2agNRxOI0-RjcqqVOpk_YLoz7l1O4SoJc?width=2400',
        'heist' => 'https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9ydkt0VEVEQWNwVmdjeGRjclpuZy5wbmcifQ:supercell:LkxXa7XSNZc_rUGLtld2sMcXLFbpRB8CE8TOjMHeMrw?width=2400',
        'wipeout', 'wipeout5V5' => 'https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9ZeTNmY05nNmtUQUgyYTZXaFlLSy5wbmcifQ:supercell:XkHJpw3hl0WdFfG-yktD4xYCsxhoPwVGXH1xT6u8l-U?width=2400',
        'duels' => 'https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9QSk0zTWhQNmtQbjkyRURZUHJEaS5wbmcifQ:supercell:Wd8Ca82s4oPNoLCceu5m8pZIFC8r2bo4Ofk8XAkRWQ4?width=2400',
        'lastStand' => '',
        'showdown+' => 'https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9pTGZmZ1VzRHl4TGVkTmdhZHlHci5wbmcifQ:supercell:xJoRSwm8tfzP4REF5Ifzlp7XT-V8s5dSWgD7GLeCNcw?width=2400',
        'basketBrawl' => 'https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9kRXNjZ0I4WFpUeFJxcWJSdUpzcC5wbmcifQ:supercell:UvHUQanzK8P3By1EDraSZGdwl8LGoUU8ciDuRHWOx3Y?width=2400',
        'volleyBrawl' => 'https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9nN2p3Q2RWaTYzQjFVTHVHdWtQUy5wbmcifQ:supercell:uwM_vqXe-_2agNRxOI0-RjcqqVOpk_YLoz7l1O4SoJc?width=2400',
        'trophyThieves' => 'https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC90TllrQXFRaFRMWXVxTlE1S21kcC5wbmcifQ:supercell:W6zxz8RwLdVjIaH2ERXU4YxzVH17TQVFZmVzcbZL7m0?width=2400',
        'bossFight' => 'https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9RZDJFd05meXVRNXZpbmtjeE05My5wbmcifQ:supercell:SNlc9xgEX-grTtTreHT-wLiOT82aDgfWBe7JjN8aFsk?width=2400',
        default => 'https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9MWVRFaWpHRXd0WVQ2UXpUelMxNC5wbmcifQ:supercell:AzQrPI9WkFdSdlvYA0Cz9PqD_V5Bw_gMtIU1qhAyNOM?width=2400',
    };
}

function traduireBannerSpecial($mode) {
    return match($mode) {
        'showdown+' => 'https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC9pTGZmZ1VzRHl4TGVkTmdhZHlHci5wbmcifQ:supercell:xJoRSwm8tfzP4REF5Ifzlp7XT-V8s5dSWgD7GLeCNcw?width=2400',
        default => '',
    };
}

function traduireModifier($mod) {
    return match($mod) {
        'unknown' => 'icons/ange-modifier.png',
        'fastBrawlers' => 'icons/fastBrawler-modifier.png',
        'superCharge' => 'icons/superCharge.png',
        default => 'icons/unknown-modifier.png',
    };
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


$specialSlotIds = [7, 8, 10]; // slotId spéciaux
$defiSlotIds = [20, 21, 22];
$defiEvents = [];
$specialEvents = [];
$classicEvents = [];

foreach ($events as $event) {
    if (in_array($event['slotId'], $specialSlotIds)) {
        $specialEvents[] = $event;
    } elseif (in_array($event['slotId'], $defiSlotIds)) {
        $defiEvents[] = $event;
    } else {
        $classicEvents[] = $event;
    }
}




?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta name="description" content="Découvrez les statistiques des joueurs et des clubs de Brawl Stars.">
  <meta name="keywords" content="brawl stars, brawl, stats, tracker, brawl rank">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Brawl Rank - Accueil</title>
  <link rel="shortcut icon" href="/icons/BSRank.ico"/>
  <link rel="apple-touch-icon" href="/icons/BSRank.ico"/>
  <link rel="stylesheet" href="style.css">

  <!-- Déclare que c'est une Web App iOS -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="Brawl Rank">

<!-- Icône d'app (doit faire au moins 180x180) -->
<link rel="apple-touch-icon" href="/icons/BSRank.ico">
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
<body>

<div class="saison" style="font-family: 'LilitaOne', sans-serif; -webkit-text-stroke-width: 0.6px; -webkit-text-stroke-color: #000; -webkit-font-smoothing: antialiased; height: 100px; position: absolute; width: 314px; top: 0; left: 0;   letter-spacing: -1px;"><h1>
  <img src='https://cdn-assets-eu.frontify.com/s3/frontify-enterprise-files-eu/eyJwYXRoIjoic3VwZXJjZWxsXC9maWxlXC91MkxhTUI3UHV3VEZkZlY1Nkc1SC5wbmcifQ:supercell:894Ug95BKwMFED3J8TC6odDCRvhLlMTMWEkvhtoX--4?width=2400' style='height: 100px; position: absolute; left: 0; top: 0;'>
  <p style="position: absolute; top: 0; right: 26px; font-size: 20px; color: rgb(255, 255, 209);" class="text-saison">Saison 41</p>
  <p style="position: absolute; top: 20px; right: 28px; font-size: 25px; text-shadow: 0px 2px 0px black;" class="text-saison">Chevaliers de la table Starr</p>
</div>
<div style="margin-top: 85px;">
  <h1 style="margin-top: 0px;">Page d'accueil</h1>
  <p>Bienvenue <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'visiteur' ?>.</p>
</div>

<h1>Évènements en cours :</h1>
<?php if (!empty($specialEvents)): ?>
  <?php if (!empty($defiEvents)): ?>
    <h2>DÉFI</h2>
    <div class="events">
<?php foreach ($defiEvents as $event): ?>
  <?php 
  $mode = $event['event']['mode'];
  $map = $event['event']['map'];
  $mapId = $event['event']['id'];
  $endTimeUTC = DateTime::createFromFormat('Ymd\THis.u\Z', $event['endTime'], new DateTimeZone('UTC'));
  $slot = $event['slotId'];

  ?>
  
<?php
$bgColor = getEventColor($mode);
$bgImage = "https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/maps/regular/$mapId.png"; // peut nécessiter ajustement selon le format exact
?>
<div class="event" style="">
  <div class="event-head" style="align-items: right; background-color: black; background-size: cover; background-position: center;">
    <span class="timer" style="font-size: 15px;" data-end="<?= $endTimeUTC->format(DateTime::ATOM) ?>"></span>
  </div>
  <div class="event-body" style="<?= $bgColor ?>; display: flex; flex-direction: row;">
    <div class="event-mode"><?= traduireImage($mode) ?></div>
    <div class="event-infos" style="display: flex; flex-direction: column;">
      <div class="event-mode"><strong><?= traduireMode($mode) ?></strong></div>
      <div class="event-map"><?= htmlspecialchars($map) ?></div>
    </div>
  </div>
  <div class="event-foot">
    <div class="image-box">
    <img src="<?= traduireBanner($mode) ?>">
    </div>
  </div>
</div>
  
<?php endforeach; ?>
<?php endif; ?>
  </div>
  <h2>ÉVÉNEMENTS SPÉCIAUX</h2>
  <div class="special-events">
  <?php foreach ($specialEvents as $event): ?>
    <?php 
      if (isset($event['event']['modifiers']) && in_array('showdown+', $event['event']['modifiers'])) {
        $mode = 'showdown+' ;
      } else {
        $mode = $event['event']['mode'];
      }


      $map = $event['event']['map'];
      $mapId = $event['event']['id'];
      $endTimeUTC = DateTime::createFromFormat('Ymd\THis.u\Z', $event['endTime'], new DateTimeZone('UTC'));
      $bgColor = getEventColor($mode);
      $bannerImg = traduireBannerSpecial($mode);
      $modifiers = $event['event']['modifiers'] ?? [];
    ?>
    <div class="special-event" style="<?= $bgColor ?>">
      <div class="special-event-head" style="background-color: black;">
        <span class="timer" style="font-size: 13px;" data-end="<?= $endTimeUTC->format(DateTime::ATOM) ?>"></span>
      </div>
      <div class="special-event-body" style="">
        <div class="special-event-infos" style="display: flex; flex-direction: column; text-align: center; align-items: center;">
          <div class="special-event-mode"><?= traduireImage($mode) ?></div>
          <div class="special-event-mode" style="margin-top: 5px;"><strong><?= traduireMode($mode) ?></strong></div>
          <div class="special-event-map" style="margin-top: 5px; white-space: wrap;"><?= htmlspecialchars($map) ?></div>
        </div>
          <div class="special-image-box">
          <?php if ($mode !== 'unknown') {
            echo '<img src="'. $bannerImg .'" style="text-align: center; position: absolute; width: 180px; bottom: 0px;" >';
          } else {}
          ?>
          <?php if (!empty($modifiers)): ?>
                    <div class="modifiers" style="margin-top: 5px;">
                        <?php foreach ($modifiers as $mod): ?>
                        <?php if ($mod !== "showdown+"): ?>
                            <img src="<?= traduireModifier($mod) ?>"style="height: 34px; width: auto; right: 9px; bottom: 6px;"></img>
                          <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
          </div>
    </div>
    </div>
  <?php endforeach; ?>
  </div>
<?php endif; ?>
<h2>ÉVÉNEMENT DE TROPHÉE</h2>
<div class="events">
<?php foreach ($classicEvents ?? [] as $event): ?>
  <?php 
  $mode = $event['event']['mode'];
  $map = $event['event']['map'];
  $mapId = $event['event']['id'];
  $endTimeUTC = DateTime::createFromFormat('Ymd\THis.u\Z', $event['endTime'], new DateTimeZone('UTC'));
  $slot = $event['slotId'];
  $modifiers = $event['event']['modifiers'] ?? [];

  ?>
  <!--<?php if ($mode !== 'unknown'): ?>-->
<?php
$bgColor = getEventColor($mode);
$bgImage = "https://raw.githubusercontent.com/Brawlify/CDN/refs/heads/master/maps/regular/$mapId.png"; // peut nécessiter ajustement selon le format exact
?>
<div class="event" style="">
  <div class="event-head" style="align-items: right; background-color: black; background-size: cover; background-position: center;">
    <span class="timer" style="font-size: 15px;" data-end="<?= $endTimeUTC->format(DateTime::ATOM) ?>"></span>
  </div>
  <div class="event-body" style="<?= $bgColor ?>; display: flex; flex-direction: row;">
    <div class="event-mode"><?= traduireImage($mode) ?></div>
    <div class="event-infos" style="display: flex; flex-direction: column;">
      <div class="event-mode"><strong><?= traduireMode($mode) ?></strong></div>
      <div class="event-map"><?= htmlspecialchars($map) ?></div>
    </div>
  </div>
  <div class="event-foot">
    <div class="image-box">
    <img src="<?= traduireBanner($mode) ?>" style="bottom: 0;">
    <?php if (!empty($modifiers)): ?>
                    <div class="modifiers" style="margin-top: 5px;">
                        <?php foreach ($modifiers as $mod): ?>
                        <?php if ($mod !== "showdown+"): ?>
                            <img src="<?= traduireModifier($mod) ?>"style="height: 34px; width: auto; right: 9px; bottom: 6px;"></img>
                          <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
    </div>
  </div>
</div>
  <!--<?php endif; ?>-->
<?php endforeach; ?>
  </div>
  <?php if (!empty($upcomingEvents)): ?>
  <h2>ÉVÉNEMENTS À VENIR</h2>
  <div class="events">
    <?php foreach ($upcomingEvents as $event): ?>
      <?php 
        $mode = $event['event']['mode'];
        $map = $event['event']['map'];
        $mapId = $event['event']['id'];
        $fixedTime = preg_replace('/\.(\d{3})Z$/', '.$1000Z', $event['startTime']);
        $startTimeUTC = DateTime::createFromFormat('Ymd\THis.u\Z', $fixedTime, new DateTimeZone('UTC'));
        $bgColor = getEventColor($mode);
      ?>
      <div class="event">
        <div class="event-head" style="background-color: black;">
          <span class="timer" style="font-size: 15px;" data-end="<?= $startTimeUTC->format(DateTime::ATOM) ?>" data-upcoming="true"></span>
        </div>
        <div class="event-body" style="<?= $bgColor ?>; display: flex;">
          <div class="event-mode"><?= traduireImage($mode) ?></div>
          <div class="event-infos" style="display: flex; flex-direction: column;">
            <div class="event-mode"><strong><?= traduireMode($mode) ?></strong></div>
            <div class="event-map"><?= htmlspecialchars($map) ?></div>
          </div>
        </div>
        <div class="event-foot">
          <div class="image-box">
            <img src="<?= traduireBanner($mode) ?>">
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<h2 style="text-align: center;"><img src="icons/leaderboards.png" style="height: 30px; transform: translateY(5px); margin-right: 3px;"">Classements</h2>
<div style="text-align:center; margin:20px;">
    <label for="type">Type :</label>
    <select id="type" onchange="loadClassement()">
        <option value="players">Joueurs</option>
        <option value="clubs">Clubs</option>
        <option value="brawlers">Brawlers</option>
    </select>

    <label for="region">  Région :</label>
    <select id="region" onchange="loadClassement()">
        <option value="global">🌍 Global</option>
        <option value="fr">🇫🇷 France</option>
        <option value="us">🇺🇸 États-Unis</option>
        <option value="de">🇩🇪 Allemagne</option>
        <option value="es">🇪🇸 Espagne</option>
        <option value="it">🇮🇹 Italie</option>
        <option value="ca">🇨🇦 Canada</option>
    </select>
    
  <!-- Sélecteur de Brawler, caché par défaut -->
    <select id="brawler" style="display:none;" onchange="loadClassement()">
      <option value="">-- Choisir un Brawler --</option>
    </select>
</div>

<div id="classement-container" style="margin-top:20px; text-align:center;">
    Chargement du classement...
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
    const diff = endTime - now;

    if (diff > 0) {
      const totalSeconds = Math.floor(diff / 1000);
      const days = Math.floor(totalSeconds / (3600 * 24));
      const hours = Math.floor((totalSeconds % (3600 * 24)) / 3600);
      const minutes = Math.floor((totalSeconds % 3600) / 60);
      const seconds = totalSeconds % 60;

      const label = timer.getAttribute('data-upcoming') === "true" ? "Début dans :" : "Fin dans :";
      const dayStr = days > 0 ? `${days}j ` : ""; // n'affiche rien
      timer.textContent = `${label} ${dayStr} ${hours}h ${minutes}m ${seconds}s`;
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



// Récupération dynamique des Brawlers
const brawlerSelect = document.getElementById("brawler");
fetch('getBrawlers.php')
    .then(res => res.json())
    .then(data => {
        data.forEach(b => {
            const option = document.createElement('option');
            option.value = b.id;      // ID pour l'API
            option.textContent = b.name; // Nom visible
            brawlerSelect.appendChild(option);
        });
    })
    .catch(console.error);

// Fonction pour charger le classement
function loadClassement() {
    const type = document.getElementById("type").value;
    const region = document.getElementById("region").value;
    const brawlerSelect = document.getElementById("brawler");
    const container = document.getElementById("classement-container");

    if (type === "brawlers") {
        brawlerSelect.style.display = "inline-block";
        // Si aucun Brawler n'est sélectionné, ne rien afficher
        if (!brawlerSelect.value) {
            container.innerHTML = "Veuillez sélectionner un Brawler.";
            return;
        }
    } else {
        brawlerSelect.style.display = "none";
        brawlerSelect.value = "";
    }

    const brawler = type === "brawlers" ? brawlerSelect.value : "";

    container.innerHTML = "⏳ Chargement...";

    fetch(`classement.php?type=${type}&region=${region}&brawler=${brawler}`)
        .then(res => res.text())
        .then(data => container.innerHTML = data)
        .catch(error => {
            container.innerHTML = "❌ Erreur de chargement";
            console.error(error);
        });
}

// Charger par défaut
loadClassement();
</script>
<p style="margin-top: 60px;"> </p>
<?php include 'navbar.php'; ?>

</body>
</html>