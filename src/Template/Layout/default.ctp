<!DOCTYPE html>
<html>
<head>

    <?= $this->Html->css([
        '//maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css'
    ]) ?>
    <?= $this->fetch('css') ?>
</head>
<body>

<div class="container" id="wrap">
    <?= $this->fetch('content') ?>
</div>
</body>
</html>