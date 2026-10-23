<?php
/** Variables : $avis */
require __DIR__ . '/_onglets.php';
?>
<h2>Tous les avis (<?= count($avis) ?>)</h2>
<p class="muted">Supprimez les commentaires abusifs : la note moyenne de la personne est recalculée automatiquement.</p>
<?php $retour = '/admin/avis'; require __DIR__ . '/_table-avis.php'; ?>
