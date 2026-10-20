<?php
/**
 * Composant réutilisable : la ligne de trajet (origine → destination).
 * Usage : <?php $origine = 'Paris'; $destination = 'Lille'; $dateHeure = '2026-11-27 17:30:00'; require ROOT . '/app/views/partials/route.php'; ?>
 */
?>
<div class="route">
    <div class="route__stop">
        <span class="route__dot" aria-hidden="true"></span>
        <span class="route__city"><?= e($origine) ?></span>
        <?php if (!empty($dateHeure)): ?><span class="route__time"><?= e(date_fr($dateHeure)) ?></span><?php endif; ?>
    </div>
    <div class="route__stop route__stop--end">
        <span class="route__dot" aria-hidden="true"></span>
        <span class="route__city"><?= e($destination) ?></span>
    </div>
</div>
