<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Bonjour {0} {1},", [h($user->firstname), h($user->lastname)]); ?><br>
</p>
<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Vous venez de modifier le mot de passe associé au compte {0}.", [$user->email]) ?>
</p>