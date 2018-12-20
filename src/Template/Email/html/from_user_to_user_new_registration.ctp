<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Bonjour ") . $user->fullname ?>,<br>
    <?= __("Nous vous remercions d'avoir créé un compte sur MairesetCitoyens.fr."); ?>
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
<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left; font-weight:bold;">
    <?= __("Selon les préférences de votre maire quant à l'acceptation d'un nouvel administré, vous devrez peut-être attendre une validation de sa part avant de pouvoir suivre son fil d'actualités.<br>
Dans le cas où votre maire ne posséderait pas encore de compte sur MairesetCitoyens.fr, vous recevrez une notification par email dès que votre maire sera inscrit sur la plateforme.") ?>
</p>
<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("En utilisant le service, vous vous engagez à :<br>
- utiliser votre nom réel et non un pseudonyme<br>
- ne publier aucune information diffamatoire<br>
- rester poli et courtois en toutes circonstances") ?>
</p>
<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Cette plateforme n'est pas politique. Il est interdit de faire l'apologie d'un parti ou de donner des avis politiques.
    Votre maire est le défenseur de tous les administrés de votre ville.") ?>
</p>
<br/>
<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <b><?= __("Veuillez trouver en pièce jointe un mode d'emploi au format PDF, qui vous donnera de précieuses informations afin d'utiliser efficacement cet outil.") ?></b>
</p>
