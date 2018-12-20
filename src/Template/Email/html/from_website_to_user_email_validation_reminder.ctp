<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Bonjour {0}", [$user->fullname]) ?>,<br>
    <?= __("Vous avez créé un compte sur MairesetCitoyens.fr le {0} mais vous n'avez pas confirmé votre adresse email.", [$user->created->format('d/m/Y')]) ?>
    <br>
</p>


<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Veuillez cliquer sur le lien ci-dessous pour terminer votre inscription et activer votre compte.") ?>
</p>

<p style="font-family: arial; color:#292929; font-size:13px; margin-bottom:20px; margin-top:40px; text-align: center;">
    <a style="text-decoration: none;font-family: arial; font-weight:600; color: #fff; padding: 10px 20px; background: #1f529b"
       href="<?= $url ?>">
        <?= __("Confirmer mon compte") ?>
    </a>
</p>
