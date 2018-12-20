<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("{0} vous a donné procuration pour administrer sa page sur MairesetCitoyens.fr.", [h($mayor_name)]) ?>
</p>
<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Voici vos informations de connexion :") ?> <br>
    Identifiant : <?= $mandatary->user->email ?> <br>
    Mot de passe temporaire : <?php echo $new_password; ?>
</p>
<p style="font-family: arial; color:#292929; font-size:13px; margin-bottom:40px; margin-top:40px; text-align: center;">
    <a style="text-decoration: none;font-family: arial; font-weight:600; color: #fff; padding: 10px 20px; background: #1f529b"
       href="<?= $url ?>">
        <?=  __("Accéder au site") ?>
    </a>
</p>
