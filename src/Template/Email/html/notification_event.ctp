<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Bonjour {0}", [$user->fullname]) ?>,<br>
    <?= __("{0} vient de créer un évènement sur l'application Deliryades", $event->user->fullname); ?>
</p>

<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: center;">
    <?= $event->title ?>
    <br>
    <?= $event->description ?>
    <br>
    <?= $event->start ?>
    <br>
    <?= $event->end ?>

    <?php if ($event->price) : ?>
        <br>
        Tarif : <?= $event->price ?>€
    <?php endif ?>

    <?php if ($event->cellphone) : ?>
        <br>
        Téléphone portable : <?= $event->cellphone ?>
    <?php endif ?>

    <?php if ($event->phone) : ?>
        <br>
        Téléphone fixe : <?= $event->phone ?>
    <?php endif ?>

    <?php if ($event->street_number) : ?>
        <br>
        Lieu de l'évènement : <?= $event->street_number ?> <?= $event->route ?> <?= $event->postal_code ?> <?= $event->locality ?> <?= $event->country ?>
    <?php endif ?>
</p>

<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Plus d'informations sur l'application") ?>
</p>

<p style="font-family: arial; color:#292929; font-size:13px; margin-bottom:20px; margin-top:40px; text-align: center;">
    <a style="text-decoration: none;font-family: arial; font-weight:600; color: #fff; padding: 10px 20px; background: #1f529b"
       href="<?= $url ?>">
        <?= __("Ouvrir l'application") ?>
    </a>
</p>