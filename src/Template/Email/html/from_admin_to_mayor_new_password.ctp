<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?php if ($mayor->sex == 'm') : ?>
        <?= __("Monsieur le maire {0} {1}", [h($mayor->firstname), h($mayor->lastname)]) ?>,<br>
    <?php else: ?>
        <?= __("Madame le maire {0} {1}", [h($mayor->firstname), h($mayor->lastname)]) ?>,<br>
    <?php endif; ?>
</p>
<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Le mot de passe de connexion associé  au compte {0} vient d'être réinitialisé.", [$mayor->email]) ?>
</p>
