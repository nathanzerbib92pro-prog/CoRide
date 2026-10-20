<section class="error-page">
    <p class="error-page__code"><?= e($code) ?></p>
    <h1><?= e($title) ?></h1>
    <?php if ($message): ?><p><?= e($message) ?></p><?php endif; ?>
    <a class="btn" href="<?= url('/') ?>">Revenir à l'accueil</a>
</section>
