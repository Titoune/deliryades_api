<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Bonjour {0}", [$user->fullname]) ?>,<br>
    <?= __("Vous avez demander un nouveau mot de passe"); ?>
</p>

<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: center;">
    <?= __("Voici votre mot de passe provisoire :") ?>
    <br>
    <?= $password ?>
</p>

<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("N'oubliez pas de le changer à la prochaine connexion.") ?>
</p>

<p style="font-family: arial; color:#292929; font-size:13px; margin-bottom:20px; margin-top:40px; text-align: center;">
    <a style="text-decoration: none;font-family: arial; font-weight:600; color: #fff; padding: 10px 20px; background: #1f529b"
       href="<?= $url ?>">
        <?= __("Ouvrir l'application") ?>
    </a>
</p>