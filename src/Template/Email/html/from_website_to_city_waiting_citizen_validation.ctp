<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?php if ($mayor && $mayor->sex == true) : ?>
        <?= __("Madame le maire {0}", [$mayor ? $mayor->fullname : '']) ?>,<br>
    <?php else: ?>
        <?= __("Monsieur le maire {0}", [$mayor ? $mayor->fullname : '']) ?>,<br>
    <?php endif; ?>
    <?= __("Un administré de votre commune vient d'essayer de s'inscrire sur MairesetCitoyens.fr, mais il semblerait que vous ne possédez pas encore de compte sur ce nouveau réseau social dédié aux maires de France."); ?>
</p>

<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Afin de connaître les modalités d'inscription et de création de votre compte maire, qui vous permettra d'optimiser les relations et d'échanger avec vos administrés en temps réel sur les décisions et actualités de votre commune, contactez-nous par téléphone au 0 892 432 404 (0.40€/min) ou par mail à l'adresse suivante : {0}", ['<a href="mailto:inscription@MairesetCitoyens.fr">inscription@MairesetCitoyens.fr</a>']) ?>
</p>


<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("A bientôt sur {0}", [$this->Html->link('MairesetCitoyens.fr', 'https://MairesetCitoyens.fr')]) ?>
</p>
