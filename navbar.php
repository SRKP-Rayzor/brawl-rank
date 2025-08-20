<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$current = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
  .navbar-bottom {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 60px;
    background-color: #222;
    display: flex;
    justify-content: space-around;
    align-items: center;
    z-index: 99999;
  }

  .navbar-bottom a {
    color: white;
    font-size: 24px;
    text-decoration: none;
  }

  .navbar-bottom a.active {
    color: rgb(252, 189, 59);
  }

  .navbar-bottom a:hover {
    color:rgb(252, 189, 59);
  }
</style>

<div class="navbar-bottom">
  <a href="index.php" class="<?= ($current === 'index.php') ? 'active' : '' ?>"><i class="fas fa-home"></i></a>
  <a href="recherche.php" class="<?= ($current === 'recherche.php') ? 'active' : '' ?>"><i class="fas fa-search"></i></a>
  <?php if (isset($_SESSION['username'])): ?>
    <a href="profil.php" class="<?= ($current === 'profil.php') ? 'active' : '' ?>"><i class="fas fa-user"></i></a>
    <a href="club.php" class="<?= ($current === 'club.php') ? 'active' : '' ?>"><i class="fas fa-user-group"></i></a>
    <a href="parametres.php" class="<?= ($current === 'parametres.php') ? 'active' : '' ?>"><i class="fas fa-cog"></i></a>
  <?php else: ?>
    <a href="login.php" class="<?= ($current === 'login.php') ? 'active' : '' ?>"><i class="fas fa-sign-in-alt"></i></a>
  <?php endif; ?>
</div>