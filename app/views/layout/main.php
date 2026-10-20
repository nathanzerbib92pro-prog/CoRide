<?php
/** Gabarit principal. Variables : $content (HTML de la vue), $pageTitle */
$current = Auth::user();
$flashes = Session::pullFlashes();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ? $pageTitle . ' – ' : '') ?>CoRide</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Overpass:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body>
<header class="site-header">
    <div class="wrap site-header__inner">
        <a href="<?= url('/') ?>" class="brand" aria-label="Accueil CoRide">
            <span class="brand__sign">Co</span><span class="brand__name">Ride</span>
        </a>
        <nav class="nav" aria-label="Navigation principale">
            <a href="<?= url('/trajets') ?>" class="<?= is_active('/trajets') ? 'is-active' : '' ?>">Trouver un trajet</a>
            <?php if ($current): ?>
                <a href="<?= url('/trajets/publier') ?>" class="<?= is_active('/trajets/publier') ? 'is-active' : '' ?>">Publier</a>
                <a href="<?= url('/mes-trajets') ?>" class="<?= is_active('/mes-trajets') ? 'is-active' : '' ?>">Mes trajets</a>
                <a href="<?= url('/mes-reservations') ?>" class="<?= is_active('/mes-reservations') ? 'is-active' : '' ?>">Mes réservations</a>
                <?php if ($current->isAdmin()): ?>
                    <a href="<?= url('/admin') ?>" class="<?= is_active('/admin') ? 'is-active' : '' ?>">Admin</a>
                <?php endif; ?>
                <a href="<?= url('/profil/' . $current->getId()) ?>" class="avatar" title="Mon profil public"><?= e($current->getInitiales()) ?></a>
                <form method="post" action="<?= url('/deconnexion') ?>" class="nav__logout">
                    <?= Csrf::field() ?>
                    <button type="submit" class="link-button">Se déconnecter</button>
                </form>
            <?php else: ?>
                <a href="<?= url('/connexion') ?>" class="<?= is_active('/connexion') ? 'is-active' : '' ?>">Se connecter</a>
                <a href="<?= url('/inscription') ?>" class="btn btn--small">Créer un compte</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="wrap main">
    <?php foreach ($flashes as $flash): ?>
        <p class="flash flash--<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></p>
    <?php endforeach; ?>

    <?= $content ?>
</main>

<footer class="site-footer">
    <div class="wrap">Projet étudiant PHP/MySQL en MVC. Les trajets sont proposés entre étudiants du campus.</div>
</footer>
</body>
</html>
