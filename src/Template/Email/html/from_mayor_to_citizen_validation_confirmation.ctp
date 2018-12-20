<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Bonjour {0}", [$user->fullname]) ?>,<br>
    <?= __("Nous vous remercions d'avoir créé un compte sur MairesetCitoyens.fr.") ?><br>
    <?= __("Vous êtes bien administré(e) ou habitant(e) de la ville de {0}, et votre maire a validé votre inscription. Vous allez maintenant pouvoir communiquer en permanence avec lui, donner votre opinion et vous tenir informé(e) des actualités de votre commune.", [$city->name]); ?>
    <br>
</p>

<p style="font-family: arial; color:#292929; font-size:13px; margin-bottom:10px; margin-top:40px; text-align: left;">
    <a style="text-decoration: none;font-family: arial; font-weight:600; color: #fff; padding: 10px 20px; background: #1f529b"
       href="<?= $url ?>">
        <?=  __("Accéder au site") ?>
    </a>
</p>