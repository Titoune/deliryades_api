<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Bonjour ") ?>,<br>
    <?= __("Un maire vient de faire une demande d'étude de devis via la centrale d'achats. Voici toutes les informations utiles (le devis initial est en PJ) :"); ?>
</p>

<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">

    <strong>Date de la demande</strong> : <?= $city_negociation->created ?> <br>
    <strong>Ville</strong> : <?= $user->city->name ?><br>
    <strong>Maire</strong> : <?= $user->fullname ?><br>
    <strong>Objet de la demande</strong> : <?= $city_negociation->name ?><br>
    <strong>Type de devis</strong> : <?= (new App\View\AppView())->getNegociationTypes()[$city_negociation->type] ?><br>
    <strong>Nom du contact à la marie</strong> : <?= h($city_negociation->incharge) ?><br>
    <strong>Téléphone du contact à la marie</strong> : <?= h($city_negociation->phone) ?><br>
    <strong>Email du contact à la marie</strong> : <a
            href='mailto:<?= $city_negociation->email ?>'><?= $city_negociation->email ?></a><br>
    <strong>Description</strong> : <br><?= h($city_negociation->description) ?><br>
    <strong>Infos complémentaires</strong> : <br><?= h($city_negociation->information) ?><br>

</p>
<br/>
<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:30px; text-align: center;">
    <a style="text-decoration: none;font-family: arial; font-weight:600; color: #fff; padding: 10px 20px; background: #1f529b; margin-bottom:20px;"
       href="<?= $url ?>">
        <?= __("Traiter cette demande et avertir le maire") ?>
    </a>

</p>