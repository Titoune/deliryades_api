<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: justify;">
    Bonjour,
    <br>
    <?= $status; ?>
</p>

<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    Pour rappel, voici les informations fournies : <br><br>
    <strong>Date de la demande</strong> : <?= $city_negociation->created ?> <br>
    <strong>Objet de la demande</strong> : <?= $city_negociation->name ?><br>
    <strong>Type de devis</strong> : <?= (new App\View\AppView())->getNegociationTypes()[$city_negociation->type] ?><br>
    <strong>Nom du contact à la marie</strong> : <?= h($city_negociation->incharge) ?><br>
    <strong>Téléphone du contact à la marie</strong> : <?= h($city_negociation->phone) ?><br>
    <strong>Email du contact à la marie</strong> : <a
            href='mailto:<?= $city_negociation->email ?>'> <?= $city_negociation->email ?></a><br>
    <strong>Description</strong> : <br><?= h($city_negociation->description) ?><br>
    <strong>Infos complémentaires</strong> : <br><?= h($city_negociation->information) ?><br>

</p>
