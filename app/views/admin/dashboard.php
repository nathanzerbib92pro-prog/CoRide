<?php
/** Variables : $trajetsRealises, $tauxRemplissage (?float 0–1), $comptesActifs, $nbAvis, $avis */
require __DIR__ . '/_onglets.php';
$tuiles = [
    [$trajetsRealises, $trajetsRealises > 1 ? 'trajets réalisés' : 'trajet réalisé'],
    [$tauxRemplissage === null ? '—' : round($tauxRemplissage * 100) . ' %', 'taux de remplissage moyen'],
    [$comptesActifs, $comptesActifs > 1 ? 'comptes actifs' : 'compte actif'],
    [$nbAvis, $nbAvis > 1 ? 'avis publiés' : 'avis publié'],
];
?>
<div class="dashboard__grid" style="margin-bottom:2.5rem">
    <?php foreach ($tuiles as [$valeur, $libelle]): ?>
        <div class="panel">
            <p style="font-size:2.75rem;font-weight:800;color:var(--ink);margin:0"><?= e($valeur) ?></p>
            <p><?= e($libelle) ?></p>
        </div>
    <?php endforeach; ?>
</div>

<h2>Avis à modérer</h2>
<?php $retour = '/admin'; require __DIR__ . '/_table-avis.php'; ?>
<p style="margin-top:1rem"><a href="<?= url('/admin/avis') ?>">Voir tous les avis</a></p>
