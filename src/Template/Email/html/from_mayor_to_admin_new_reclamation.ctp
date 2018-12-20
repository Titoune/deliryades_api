<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("{0} {1} vient de faire une réclamation concernant une publicité", [h($user->firstname), h($user->lastname)]) ?>
</p>
<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Voici les informations de la publicité concernée :") ?> <br>
    Titre : <?= $ad->ad->title ?> <br>
    Annonceur : <?= $ad->ad->ad_client->company ?><br>
    Date de début : <?= $ad->ad->date_start ?>
</p>
<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Voici le message du maire :") ?> <br>
    <?= $message ?>
</p>
