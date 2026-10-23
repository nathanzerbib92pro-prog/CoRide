<?php
/** Variables : $utilisateurs (Utilisateur[]) */
require __DIR__ . '/_onglets.php';
?>
<h2>Comptes (<?= count($utilisateurs) ?>)</h2>
<p class="muted">Un compte désactivé ne peut plus se connecter. On ne supprime pas : ses trajets, réservations et avis restent dans l'historique.</p>
<div class="table-wrap">
    <table>
        <thead>
            <tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Note moyenne</th><th>Inscription</th><th>État</th><th>Action</th></tr>
        </thead>
        <tbody>
            <?php foreach ($utilisateurs as $u): ?>
                <?php $moyenne = Avis::noteMoyenne($u->getId()); ?>
                <tr>
                    <td><a href="<?= url('/profil/' . $u->getId()) ?>"><?= e($u->getNom()) ?></a></td>
                    <td><?= e($u->getEmail()) ?></td>
                    <td><?= $u->isAdmin() ? 'Admin' : 'Utilisateur' ?></td>
                    <td>
                        <?php if ($moyenne === null): ?>
                            <span class="note note--vide">Pas encore noté</span>
                        <?php else: ?>
                            <span class="note"><span class="note__star" aria-hidden="true">★</span> <?= e(number_format($moyenne, 1, ',', ' ')) ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?= e(date('d/m/Y', strtotime($u->getCreatedAt()))) ?></td>
                    <td>
                        <span class="statut <?= $u->isActif() ? 'statut--ouvert' : 'statut--refusee' ?>"><?= $u->isActif() ? 'Actif' : 'Désactivé' ?></span>
                    </td>
                    <td>
                        <?php if ($u->getId() === Auth::id()): ?>
                            <span class="muted">Vous</span>
                        <?php else: ?>
                            <form method="post" action="<?= url('/admin/utilisateurs/' . $u->getId() . ($u->isActif() ? '/desactiver' : '/activer')) ?>"
                                <?= $u->isActif() ? 'onsubmit="return confirm(\'Désactiver ce compte ?\');"' : '' ?>>
                                <?= Csrf::field() ?>
                                <button type="submit" class="btn btn--small <?= $u->isActif() ? 'btn--danger' : 'btn--ghost' ?>">
                                    <?= $u->isActif() ? 'Désactiver' : 'Réactiver' ?>
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
