<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Bonjour") ?>,
    <br>
    <?= __("{0} a posté un signalement le {1} concernant votre commune : {2}.", [$username, $signaling->created, $city->name]); ?>
    <br>
    <br>
    <?= __("En voici les détails") ?>:
    <br>
</p>
<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <b>Catégorie :</b> <?= h($category) ?>
    <br>
    <b>Titre :</b> <?= h($signaling->title) ?>
    <br>
    <b>Description :</b> <?= nl2br(h($signaling->description)) ?>
    <br>
</p>
<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?php foreach ($pictures AS $p) : ?>
        <img src="<?= $p->picture_sizes['default'] ?>&width=180&height=115"/>
    <?php endforeach ?>

</p>
<?php if ($signaling->lat && $signaling->lng) : ?>
    <p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: center;">
        <?php if ($signaling->locality) : ?>
            Lieu du signalement: <?= $signaling->street_number ?> <?= $signaling->route ?>  <?= $signaling->postal_code ?>  <?= $signaling->locality ?>  <?= $signaling->country ?>
        <?php endif ?>
        <br>
        <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $signaling->lat ?>,<?= $signaling->lng ?>&travelmode=driving">
            <?= __("Voir l'emplacement du signalement") ?>
        </a>
        <br>
    </p>
<?php endif ?>
<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: center;">
    <br>
    <a style="text-decoration: none;font-family: arial; font-weight:600; color: #fff; padding: 10px 20px; background: #1f529b"
       href="<?= $url ?>">
        <?=  __("Voir le signalement") ?>
    </a>
    <br>
</p>
