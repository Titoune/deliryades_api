<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?php if ($mayor->sex == 'm') : ?>
        <?= __("Monsieur le maire {0} {1}", [h($mayor->firstname), h($mayor->lastname)]) ?>,<br>
    <?php else: ?>
        <?= __("Madame le maire {0} {1}", [h($mayor->firstname), h($mayor->lastname)]) ?>,<br>
    <?php endif; ?>
</p>
<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Bonjour et bienvenue sur MairesetCitoyens.fr.") ?>
    <br>
    <?= __("Nous sommes heureux de vous informer que votre compte a été activé pour la ville de {0}.", [$city->name]) ?>
</p>
<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Veuillez trouver en pièce jointe un mode d'emploi au format PDF, qui vous donnera de précieuses informations afin d'utiliser efficacement cet outil.") ?>
</p>
<br>
<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <a style="text-decoration: none;font-family: arial; font-weight:600; color: #fff; padding: 10px 20px; background: #1f529b"
       href="<?= $url ?>">
        <?=  __("Lien de connexion") ?>
    </a>
</p>
<br/>
<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Nous restons à votre disposition si vous rencontrez un quelconque souci à l'utilisation de notre plateforme.") ?>
</p>
