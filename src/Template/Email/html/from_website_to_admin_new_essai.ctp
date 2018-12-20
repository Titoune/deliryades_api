<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Bonjour") ?>,<br>
    <?= __("Une nouvelle demande d'essai est envoyée depuis le site!"); ?>
</p>

<p style="font-family: arial; color:#292929; font-size:13px; margin-bottom:20px; margin-top:40px; text-align: center;">
    <b>Prénom</b> : <?= $data['firstname'] ?>
    <br>
    <b>Nom</b> : <?= $data['lastname'] ?>
    <br>
    <b>Fonction</b> : <?= $data['function'] ?>
    <br>
    <b>Commune</b> : <?= $data['city'] ?>
    <br>
    <b>Email</b> : <?= $data['email'] ?>
    <br>
    <b>Téléphone</b> : <?= $data['phone'] ?>
</p>
