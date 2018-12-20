<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">

    <?= __("Bonjour {0} {1},", [h($user->firstname), h($user->lastname)]); ?><br>
</p>
<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Vous avez demandé la suppression de votre compte sur MairesetCitoyens.fr.") ?>
</p>
<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Si vous n'êtes pas à l'origine de cette action, ou si vous avez changé d'avis, vous pouvez annuler cette demande de suppression de compte en cliquant sur le bouton \"rétablir mon compte\", ou bien en vous connectant sur la plateforme avec vos identifiants habituels dans un délai de 72 heures.") ?>
</p>
<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Passé ce délai, cette action sera irréversible et vous ne pourrez plus vous connecter sur MairesetCitoyens.fr.") ?>
</p>