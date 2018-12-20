<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Bonjour {0} {1},", [h($user->firstname), h($user->lastname)]); ?><br>
</p>
<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Vous avez effectué une demande de réinitialisation de votre mot de passe.") ?>
</p>
<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Cliquez sur le lien ci-dessous pour changer votre mot de passe.") ?>
</p>
<p style="font-family: arial; color:#292929; font-size:13px; margin-bottom:40px; margin-top:40px; text-align: center;">
    <a style="text-decoration: none;font-family: arial; font-weight:600; color: #fff; padding: 10px 20px; background: #1f529b"
       href="<?= $url ?>">
        <?= __("Réinitialiser mon mot de passe") ?>
    </a>
</p>
<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Dans le cas où vous n’auriez pas effectué cette demande nous vous conseillons de nous contacter le plus rapidement possible.") ?>
</p>
