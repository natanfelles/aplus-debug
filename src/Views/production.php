<?php
/**
 * @var Framework\Debug\ExceptionHandler $handler
 */
?>
<!doctype html>
<html lang="<?= $handler->getLanguage()->getCurrentLocale() ?>" dir="<?= $handler->getLanguage()
    ->getCurrentLocaleDirection() ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $handler->getLanguage()->render('debug', 'exceptionTitle') ?></title>
    <style>
        body {
            background: #000;
            color: #eee;
            font-family: monospace, sans-serif;
            font-size: 1rem;
            line-height: 1.5rem;
            margin: 1rem;
        }

        h1 {
            color: #fff;
        }
    </style>
</head>
<body>
<h1><?= $handler->getLanguage()->render('debug', 'exceptionTitle') ?></h1>
<p><?= $handler->getLanguage()->render('debug', 'exceptionDescription') ?></p>
<?php if ($handler->getLogger() && $handler->getLogger()->getLastLog()?->written): ?>
    <p>Log Id: <?= $handler->getLogger()->getLastLog()->id ?></p>
<?php endif ?>
</body>
</html>
