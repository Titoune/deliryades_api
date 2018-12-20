<?php

// default
$firstName = null;
$lastName = null;
$dPrefix = '';
$dController = 'Users';
$dAction = 'login';
$dPicture = '';

if (isset($object)) {
    $firstName = isset($object->firstname) ? h($object->firstname) : h($object->firstname);
    $lastName = isset($object->lastname) ? $object->lastname : $object->lastname;
    $dPrefix = isset($object->firstname) ? 'citizenbundle' : 'mayorbundle';
    $dController = isset($object->firstname) ? 'Users' : 'Mayors';
    $dAction = 'update';
    $dPicture = $this->Image->resize(IMAGE_DEFAULT_USER, 35, 35, [
        'style' => ' border: 2px solid #c1c1c1; border-radius: 50%; -webkit-border-radius: 50%; -moz-border-radius: 50%;',
        'fullBase' => true
    ]);
    if ($object->picture) {
        $dPicture = $this->Image->resize($object->id . '/' . $object->picture, 35, 35,
            [
                'style' => ' border: 2px solid #c1c1c1; border-radius: 50%; -webkit-border-radius: 50%; -moz-border-radius: 50%;',
                'fullBase' => true
            ], MEDIA_PATH_USER, MEDIA_URL_USER);
    }

} elseif (isset($procuration)) {
    $firstName = isset($procuration) && isset($procuration->firstname) ? $procuration->firstname : null;
    $lastName = isset($procuration) && isset($procuration->lastname) ? $procuration->lastname : null;
    $dPrefix = 'mayorbundle';
    $dController = 'Mayors';
    $dAction = 'update';
    $dPicture = $this->Image->resize(IMAGE_DEFAULT_USER, 35, 35, [
        'style' => ' border: 2px solid #c1c1c1; border-radius: 50%; -webkit-border-radius: 50%; -moz-border-radius: 50%;',
        'fullBase' => true
    ]);
} elseif (isset($user) || isset($mayor)) {
    $firstName = isset($user) && isset($user->firstname) ? $user->firstname : (isset($mayor) && isset($mayor->firstname) ? $mayor->firstname : null);
    $lastName = isset($user) && isset($user->lastname) ? $user->lastname : (isset($mayor) && isset($mayor->lastname) ? $mayor->lastname : null);
    $dPrefix = isset($user) ? 'citizenbundle' : 'mayorbundle';
    $dController = isset($user) ? 'Users' : 'Mayors';
    $dAction = 'update';
    $dPicture = $this->Image->resize(IMAGE_DEFAULT_USER, 35, 35, [
        'style' => ' border: 2px solid #c1c1c1; border-radius: 50%; -webkit-border-radius: 50%; -moz-border-radius: 50%;',
        'fullBase' => true
    ]);
    if (isset($user) && isset($user->picture) && !empty($user->picture)) {
        $dPicture = $this->Image->resize($user->id . '/' . $user->picture, 35, 35,
            [
                'style' => ' border: 2px solid #c1c1c1; border-radius: 50%; -webkit-border-radius: 50%; -moz-border-radius: 50%;',
                'fullBase' => true
            ], MEDIA_PATH_USER, MEDIA_URL_USER);
    } elseif (isset($mayor) && isset($mayor->picture) && !empty($mayor->picture)) {
        $dPicture = $this->Image->resize($mayor->id . '/' . $mayor->picture, 35, 35,
            [
                'style' => ' border: 2px solid #c1c1c1; border-radius: 50%; -webkit-border-radius: 50%; -moz-border-radius: 50%;',
                'fullBase' => true
            ], MEDIA_PATH_USER, MEDIA_URL_USER);
    }
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
    <title><?= $this->fetch('title') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
</head>
<body bgcolor="#e8e9ed" style="background: #e8e9ed; margin:0; padding:0">


<table width="600" border="0" bgcolor="" style="margin: 0 auto; border-collapse: collapse; margin-top:30px;">
    <tr>
        <td bgcolor="" align="center" style="text-align:center; padding-bottom: 40px; padding-top:20px;">

        </td>
    </tr>
    <tr>
        <td style="padding:30px 20px;" bgcolor="#ffffff">
            <?php if (!is_null($firstName) && !is_null($lastName)) : ?>
                <table style="margin-bottom: 20px;">
                    <tr>
                        <td>
                            <?= $dPicture ?>
                        </td>
                        <td>
                            <p style="font-family: arial; color:#1f529b; font-size:15px; font-weight: bold; margin: 0 10px; text-align: left;">

                            </p>
                        </td>
                    </tr>
                </table>
            <?php endif; ?>

            <?= $this->fetch('content') ?>
            <p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px;margin-top:40px; text-align: left;">
                <?= __("L'équipe MairesetCitoyens.fr.") ?>
            </p>
        </td>
    </tr>
    <tr>
        <td style=" padding:30px 0;" bgcolor="">
            <?php if (in_array($this->getTemplate(), ['notifications', 'notconnected_reminder'])) : ?>
                <p style="font-family: arial; color:#292929; font-size:13px; margin-bottom:10px; text-align: center;">
                    <?= __("Vous recevez cet email car vous êtes inscrit(e) sur la plateforme MairesetCitoyens.fr et que vous avez demandé à recevoir des notifications par email.") ?>
                </p>
                <p style="font-family: arial; color:#292929; font-size:13px; margin-bottom:10px; text-align: center;">


                </p>

            <?php endif ?>

            <p style="font-family:arial; color:#292929; font-size:13px; margin-bottom:10px; text-align: center;">
                <?= __("Pour toute question, contactez-nous au 0 892 432 404 (0.40€/min)") ?>
            </p>
        </td>
    </tr>
</table>
</body>
</html>
