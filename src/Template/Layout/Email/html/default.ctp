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
            <?= $this->fetch('content') ?>
            <p style="font-family: arial; color:#292929; font-size:14px; line-height:20px; margin-bottom:10px;margin-top:40px; text-align: left;">
                <?= __("Deliryades") ?>
            </p>
        </td>
    </tr>
    <tr>
        <td style=" padding:30px 0;" bgcolor="">
            <p style="font-family:arial; color:#292929; font-size:13px; margin-bottom:10px; text-align: center;">
                <?= __("Pour toute question, contactez-moi au 06 52 83 80 56") ?>
            </p>
        </td>
    </tr>
</table>
</body>
</html>
