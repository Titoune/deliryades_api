<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Bonjour {0}", [$user->fullname]) ?>,<br>
    <?= __("{0} vient de créer un sondage sur l'application Deliryades", $poll->user->fullname); ?>
</p>

<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: center;">
    <?= $poll->question ?>
    <br>
    <?= $event->content ?>
</p>

<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Participez à ce sondage sur l'application.") ?>
</p>

<p style="font-family: arial; color:#292929; font-size:13px; margin-bottom:20px; margin-top:40px; text-align: center;">
    <a style="text-decoration: none;font-family: arial; font-weight:600; color: #fff; padding: 10px 20px; background: #1f529b"
       href="<?= $url ?>">
        <?= __("Ouvrir l'application") ?>
    </a>
</p>