<?php if (!$user): ?>
<section class="hero">
    <div class="hero__text">
        <h1>Ce trajet, quelqu'un du campus le fait déjà.</h1>
        <p>Les conducteurs publient leurs trajets, les passagers réservent une place, et chacun se note après le voyage.</p>
        <div class="hero__actions">
            <a class="btn" href="<?= url('/trajets') ?>">Trouver un trajet</a>
            <a class="btn btn--ghost" href="<?= url('/inscription') ?>">Créer un compte</a>
        </div>
    </div>
    <div class="hero__sign" aria-hidden="true">
        <div class="sign-line"><span>Gare</span><span>12 min</span></div>
        <div class="sign-line"><span>Lille</span><span>2 places</span></div>
        <div class="sign-line"><span>Retour de stage</span><span>vendredi</span></div>
    </div>
</section>
<?php else: ?>
<section class="dashboard">
    <h1>Bonjour <?= e($user->getNom()) ?></h1>
    <div class="dashboard__grid">
        <a class="panel" href="<?= url('/trajets') ?>">
            <h2>Je cherche une place</h2>
            <p>Trajets ouverts filtrés par ville et par date.</p>
        </a>
        <a class="panel" href="<?= url('/trajets/publier') ?>">
            <h2>Je conduis</h2>
            <p>Publier un trajet et gérer les demandes reçues.</p>
        </a>
        <a class="panel" href="<?= url('/mes-reservations') ?>">
            <h2>Mes réservations</h2>
            <p>En attente, confirmées ou refusées.</p>
        </a>
        <?php if ($user->isAdmin()): ?>
        <a class="panel panel--admin" href="<?= url('/admin') ?>">
            <h2>Administration</h2>
            <p>Modération des avis, comptes et statistiques.</p>
        </a>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>
