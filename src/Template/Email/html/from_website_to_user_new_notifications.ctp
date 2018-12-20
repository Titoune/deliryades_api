<p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
    <?php if ($user->mayor) : ?>
        <?= __("Bonjour {0} le {1} {2}", [$user->sex == 'f' ? 'Madame' : 'Monsieur', $user->mayor->city->account_user_type, $user->fullname]) ?>
    <?php else : ?>
        <?= __("Bonjour {0}", [$user->fullname]) ?>
    <?php endif ?>
    ,<br>
    <?= __("Vous avez {0} nouvelle(s) notification(s):", $notification_count) ?>
</p>

<?php foreach ($notifications AS $k => $n) : ?>

    <?php foreach ($n AS $k => $p) : ?>
        <p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px; text-align: left;">
            <?php if ($k == 0) : ?>
                <?= $p->city->name ?>
            <?php else : ?>
                <?php if ($p->city_id != $n[$k - 1]['city_id']) : ?>
                    <?= $p->city->name ?>
                <?php endif ?>
            <?php endif ?>
        </p>

        <p style="font-family: arial; color:#292929; font-size:13px; margin-bottom:10px; margin-top:10px; text-align: left;">
        <a style="text-decoration: none;font-family: arial; color: #1f529b; font-weight: bold"
           href="<?= $url ?>">
            <?= $p->published->nice() . ' ' . str_replace('{0}', $p->param1, str_replace('{1}', $p->param2, str_replace('{2}', $p->param3, str_replace('{3}', $p->param4, $p->notification_type->email_message)))) ?>
        </a>
        <?php if ($p->notification_type->type == 'alert') : ?>
            <p style="font-family: arial; color:#333333; font-weight: bold; font-size:13px; margin-bottom:10px; margin-top:0px; text-align: left;">
                <?= $p->param3 ?>
            </p>
        <?php endif ?>
        </p>


    <?php endforeach ?>
<?php endforeach ?>

<p style="font-family: arial; color:#292929; font-size:13px; margin-bottom:10px; margin-top:40px; text-align: left;">
    <a style="text-decoration: none;font-family: arial; font-weight:600; color: #fff; padding: 10px 20px; background: #1f529b"
       href="<?= $url ?>">
        <?=  __("Voir toutes les notifications") ?>
    </a>
</p>
