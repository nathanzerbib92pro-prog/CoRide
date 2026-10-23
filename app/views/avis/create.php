<?php
/**
 * Écran « Noter votre trajet » (maquette 08).
 * Variables : $trajet (Trajet), $cibles, $blocage (?string), $peutCloturer, $errors, $cibleEnErreur, $old
 */
$statutClasse = ['Ouvert' => 'ouvert', 'Complet' => 'complet', 'Terminé' => 'termine'][$trajet->getStatut()] ?? '';
?>
<section class="avis">
    <h1>Noter votre trajet</h1>

    <article class="trip trip--<?= e($statutClasse) ?>">
        <?php
        $origine = $trajet->getOrigine();
        $destination = $trajet->getDestination();
        $dateHeure = $trajet->getDateHeure();
        require ROOT . '/app/views/partials/route.php';
        ?>
        <div class="trip__meta">
            <span class="statut statut--<?= e($statutClasse) ?>"><?= e($trajet->getStatut()) ?></span>
        </div>
    </article>

    <?php if ($peutCloturer): ?>
        <form method="post" action="<?= url('/trajets/' . $trajet->getId() . '/cloturer') ?>" class="form" style="margin-top:1.5rem">
            <?= Csrf::field() ?>
            <p class="muted">Le trajet a eu lieu ? Clôturez-le pour ouvrir la notation. Les demandes encore en attente seront refusées.</p>
            <button type="submit" class="btn">Clôturer ce trajet</button>
        </form>
    <?php endif; ?>

    <?php if ($blocage !== null): ?>
        <p class="flash flash--info" role="status" style="margin-top:1.5rem"><?= e($blocage) ?></p>
    <?php else: ?>
        <?php foreach ($cibles as $cible): ?>
            <?php
            $enErreur = $cibleEnErreur === $cible['id'];
            $err = fn(string $f) => $enErreur && isset($errors[$f])
                ? '<span class="field__error" id="err-' . $f . '-' . $cible['id'] . '">' . e($errors[$f]) . '</span>' : '';
            $noteChoisie = $enErreur ? ($old['note'] ?? '') : '';
            ?>
            <div style="margin-top:2rem">
                <h2>Votre note pour <?= e($cible['nom']) ?> (<?= e($cible['role']) ?>)</h2>

                <?php if ($cible['deja_note']): ?>
                    <p class="muted">Vous avez déjà noté <?= e($cible['nom']) ?> sur ce trajet. Merci !</p>
                <?php else: ?>
                    <form method="post" action="<?= url('/trajets/' . $trajet->getId() . '/avis') ?>" class="form" novalidate>
                        <?= Csrf::field() ?>
                        <input type="hidden" name="id_cible" value="<?= e($cible['id']) ?>">

                        <fieldset class="field" style="border:0;padding:0;margin:0">
                            <legend style="font-weight:600;margin-bottom:.35rem">Note</legend>
                            <div style="display:flex;gap:.75rem;flex-wrap:wrap">
                                <?php for ($n = Avis::NOTE_MIN; $n <= Avis::NOTE_MAX; $n++): ?>
                                    <label style="display:flex;align-items:center;gap:.3rem;font-weight:800">
                                        <input type="radio" name="note" value="<?= $n ?>" required
                                            <?= (string) $n === $noteChoisie ? 'checked' : '' ?>>
                                        <?= $n ?> <span class="note__star" aria-hidden="true">★</span>
                                    </label>
                                <?php endfor; ?>
                            </div>
                            <?= $err('note') ?>
                        </fieldset>

                        <label class="field">
                            <span>Commentaire <small>(facultatif, <?= AvisController::COMMENTAIRE_MAX ?> caractères max.)</small></span>
                            <textarea name="commentaire" rows="4" maxlength="<?= AvisController::COMMENTAIRE_MAX ?>"
                                <?= $enErreur && isset($errors['commentaire']) ? 'aria-invalid="true"' : '' ?>><?= e($enErreur ? ($old['commentaire'] ?? '') : '') ?></textarea>
                            <?= $err('commentaire') ?>
                        </label>

                        <button type="submit" class="btn">Publier mon avis</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
