<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Bonjour {0}", [$user->fullname]) ?>,<br>
    <?= __("Votre signalement <b>\"{0}\"</b> vient d'être mis à jour par l'équipe de votre mairie.", h($signaling->title)) ?>
    <br>
</p>


<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    Le nouveau statut est :
    <strong>
        <?php
        if ($signaling->status == 1) {
            echo "En attente de traitement par les services de la mairie";

        } elseif ($signaling->status == 2) {
            echo "Pris en compte par les services de la mairie";

        } elseif ($signaling->status == 3) {
            echo "Ne peut être traité par les services de la mairie";

        } elseif ($signaling->status == 4) {
            echo "A déjà été signalé";
        }
        ?>
    </strong>
</p>

<p style="font-family: arial; color:#292929; font-size:13px; margin-bottom:20px; margin-top:40px; text-align: center;">
    <a style="text-decoration: none;font-family: arial; font-weight:600; color: #fff; padding: 10px 20px; background: #1f529b"
       href="<?= $url ?>">
        <?=  __("Accéder à mon compte") ?>
    </a>
</p>
