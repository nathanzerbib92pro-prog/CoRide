<section class="auth">
    <h1>Se connecter</h1>

    <?php if (!empty($errors['global'])): ?>
        <p class="flash flash--error" role="alert"><?= e($errors['global']) ?></p>
    <?php endif; ?>

    <form method="post" action="<?= url('/connexion') ?>" class="form" novalidate>
        <?= Csrf::field() ?>
        <label class="field">
            <span>Email</span>
            <input type="email" name="email" value="<?= e($old['email'] ?? '') ?>" required autocomplete="email">
        </label>
        <label class="field">
            <span>Mot de passe</span>
            <input type="password" name="password" required autocomplete="current-password">
        </label>
        <button type="submit" class="btn">Se connecter</button>
    </form>
    <p class="muted">Pas encore de compte ? <a href="<?= url('/inscription') ?>">Créer un compte</a></p>
</section>
