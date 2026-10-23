<?php
/**
 * Tableau des avis avec suppression (tableau de bord et page « Avis »).
 * Variables : $avis (lignes de Avis::listerPourModeration()), $retour ('/admin' ou '/admin/avis')
 */
?>
<?php if ($avis === []): ?>
    <p class="muted">Aucun avis publié.</p>
<?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Auteur → cible</th><th>Note</th><th>Commentaire</th><th>Trajet</th><th>Date</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php foreach ($avis as $a): ?>
                    <tr>
                        <td><strong><?= e($a['auteur']) ?> → <?= e($a['cible']) ?></strong></td>
                        <td class="note"><span class="note__star" aria-hidden="true">★</span> <?= e($a['note']) ?></td>
                        <td><?= $a['commentaire'] === null ? '<span class="muted">—</span>' : e($a['commentaire']) ?></td>
                        <td><?= e($a['origine']) ?> → <?= e($a['destination']) ?></td>
                        <td><?= e(date_fr($a['date'])) ?></td>
                        <td>
                            <form method="post" action="<?= url('/admin/avis/' . $a['id'] . '/supprimer') ?>"
                                  onsubmit="return confirm('Supprimer définitivement cet avis ?');">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="retour" value="<?= e($retour) ?>">
                                <button type="submit" class="btn btn--danger btn--small">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
