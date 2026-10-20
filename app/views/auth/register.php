<?php
/** Affiche le message d'erreur d'un champ, s'il y en a un. */
$err = fn(string $f) => isset($errors[$f]) ? '<span class="field__error" id="err-' . $f . '">' . e($errors[$f]) . '</span>' : '';
$inv = fn(string $f) => isset($errors[$f]) ? 'aria-invalid="true" aria-describedby="err-' . $f . '"' : '';
?>
<section class="auth">
    <h1>Créer un compte</h1>
    <p class="muted">Un seul compte pour conduire et pour voyager.</p>

    <form method="post" action="<?= url('/inscription') ?>" class="form" novalidate>
        <?= Csrf::field() ?>
        <label class="field">
            <span>Prénom et nom</span>
            <input type="text" name="nom" value="<?= e($old['nom'] ?? '') ?>" required maxlength="100" autocomplete="name" <?= $inv('nom') ?>>
            <?= $err('nom') ?>
        </label>
        <label class="field">
            <span>Email</span>
            <input type="email" name="email" value="<?= e($old['email'] ?? '') ?>" required autocomplete="email" <?= $inv('email') ?>>
            <?= $err('email') ?>
        </label>
        <label class="field">
            <span>Mot de passe <small>(8 caractères minimum)</small></span>
            <input type="password" name="password" required minlength="8" autocomplete="new-password" <?= $inv('password') ?>>
            <?= $err('password') ?>
        </label>
        <label class="field">
            <span>Confirmer le mot de passe</span>
            <input type="password" name="password_confirm" required autocomplete="new-password" <?= $inv('password_confirm') ?>>
            <?= $err('password_confirm') ?>
        </label>
        <button type="submit" class="btn">Créer mon compte</button>
    </form>
    <p class="muted">Déjà inscrit ? <a href="<?= url('/connexion') ?>">Se connecter</a></p>
</section>
