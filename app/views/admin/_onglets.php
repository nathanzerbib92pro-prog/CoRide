<?php
/** Onglets de l'espace admin (maquette 09). */
$onglets = ['/admin' => 'Vue d\'ensemble', '/admin/avis' => 'Avis', '/admin/utilisateurs' => 'Comptes'];
?>
<h1>Administration</h1>
<nav class="nav" aria-label="Administration" style="justify-content:flex-start;border-bottom:2px solid var(--mist);margin-bottom:2rem">
    <?php foreach ($onglets as $chemin => $libelle): ?>
        <a href="<?= url($chemin) ?>" class="<?= is_active($chemin) ? 'is-active' : '' ?>"><?= e($libelle) ?></a>
    <?php endforeach; ?>
</nav>
