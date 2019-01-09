<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?= __("Bonjour {0}", [$user->fullname]) ?>,<br>
    <?php if ($birthdays->count() == 1) : ?>
        <?= __("{0} personne fête son anniversaire aujourd'hui", $birthdays->count()); ?>
    <?php else : ?>
        <?= __("{0} personnes fêtent leurs anniversaires aujourd'hui", $birthdays->count()); ?>
    <?php endif ?>
</p>

<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: center;">
    <?php foreach ($birthdays AS $birthday) : ?>
        <?= $birthday->fullname ?>
        <br>
        <?= $birthday->age ?> ans aujourd'hui
        <?php if ($birthday->email) : ?>
            <br>
            Son email : <?= $birthday->email ?>
        <?php endif ?>

        <?php if ($birthday->cellphone) : ?>
            <br>
            Son téléphone portable : <?= $birthday->cellphone ?>
        <?php endif ?>

        <?php if ($birthday->phone) : ?>
            <br>
            Son téléphone fixe : <?= $birthday->phone ?>
        <?php endif ?>

        <?php if ($birthday->street_number) : ?>
            <br>
            Son adresse : <?= $birthday->street_number ?> <?= $birthday->route ?> <?= $birthday->postal_code ?> <?= $birthday->locality ?> <?= $birthday->country ?>
        <?php endif ?>
    <?php endforeach; ?>
</p>


<p style="font-family: arial; color:#292929; font-size:13px; margin-bottom:20px; margin-top:40px; text-align: center;">
    <a style="text-decoration: none;font-family: arial; font-weight:600; color: #fff; padding: 10px 20px; background: #1f529b"
       href="<?= $url ?>">
        <?= __("Ouvrir l'application") ?>
    </a>
</p>