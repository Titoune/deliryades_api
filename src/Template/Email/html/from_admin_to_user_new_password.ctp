<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Bonjour ") ?> <?= h($user->firstname) ?> <?= h($user->lastname) ?>,<br>
    <?= __("Voici vos identifiants vous permettant de vous connecter sur la plateforme MairesetCitoyens.fr"); ?>
</p>

<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <strong>Identifiant : </strong> <?= $user->email ?><br>
    <strong>Mot de passe temporaire : </strong> <?= $password ?>
</p>
