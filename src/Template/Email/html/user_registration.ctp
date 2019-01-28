<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Bonjour ") . $user->fullname ?>,<br>
    <?= __("Nous vous remercions d'avoir créé un compte sur Deliryades"); ?>
</p>

<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Veuillez cliquer sur le lien ci-dessous pour confirmer votre inscription et activer votre compte.") ?>
</p>

<p style="font-family: arial; color:#292929; font-size:13px; margin-bottom:20px; margin-top:40px; text-align: center;">
    <a style="text-decoration: none;font-family: arial; font-weight:600; color: #fff; padding: 10px 20px; background: #1f529b"
       href="<?= $url ?>">
        <?= __("Confirmer mon compte") ?>
    </a>
</p>
<br>
<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Rappel de vos identifiants :") ?>
    <?= $user->email ?> / votre mot de passe
</p>