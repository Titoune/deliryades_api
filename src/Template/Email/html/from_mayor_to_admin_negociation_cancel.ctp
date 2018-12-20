<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Bonjour ") ?>,<br>
    <?= __("Un {0} vient d'annuler une demande d'étude de devis. Voici toutes les informations de la demande initiale :", [$city_negociation->city->account_user_type]); ?>
</p>

<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">

    <strong>Date de la demande</strong> : <?= $city_negociation->created ?><br>
    <strong>Ville</strong> : <?= $city_negociation->city->name ?><br>
    <strong>Maire</strong> : <?= $city_negociation->city->user->fullname ?><br>
    <strong>Objet de la demande</strong> : <?= $city_negociation->name ?><br>
    <strong>Type de devis</strong> : <?= (new \App\View\AppView())->getNegociationTypes()[$city_negociation->type] ?>
    <br>
    <strong>Nom du contact à la marie</strong> : <?= h($city_negociation->incharge) ?><br>
    <strong>Téléphone du contact à la marie</strong> : <?= h($city_negociation->phone) ?><br>
    <strong>Email du contact à la marie</strong> : <a
            href='mailto:<?= $city_negociation->email ?>'><?= $city_negociation->email ?></a><br>
    <strong>Description</strong> : <br><?= h($city_negociation->description) ?><br>
    <strong>Infos complémentaires</strong> : <br><?= h($city_negociation->information) ?><br>

</p>
