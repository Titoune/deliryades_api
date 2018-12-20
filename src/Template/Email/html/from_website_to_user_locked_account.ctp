<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Bonjour {0}", [$object->fullname]) ?>,<br>
    <?= __("Nous avons remarqué plusieurs tentatives infructueuses de connexion sur votre compte MairesetCitoyens.fr.") ?>
    <br>
</p>


<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Par mesure de sécurité, nous avons bloqué l'accès à votre compte. Pour accéder de nouveau à votre compte,  cliquez sur le lien ci-dessous.") ?>
</p>

<p style="font-family: arial; color:#292929; font-size:13px; margin-bottom:20px; margin-top:40px; text-align: center;">
    <a style="text-decoration: none;font-family: arial; font-weight:600; color: #fff; padding: 10px 20px; background: #1f529b"
       href="<?= $url ?>">
        <?=  __("Débloquer mon compte") ?>
    </a>
</p>
