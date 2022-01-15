<?php
/**
 * @var Exception $exception
 * @var Framework\Debug\ExceptionHandler $handler
 */
?>
<!doctype html>
<html lang="<?= $handler->getLanguage()->getCurrentLocale() ?>" dir="<?= $handler->getLanguage()
    ->getCurrentLocaleDirection() ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $handler->getLanguage()->render('debug', 'exception') ?>: <?=
        htmlentities($exception->getMessage()) ?></title>
    <style>
        body {
            margin: 0 0 20px;
            font-family: sans-serif;
            background-color: whitesmoke;
        }

        header {
            background: red;
            color: white;
            border: solid black;
            border-width: 1px 0;
            padding: 20px 20px;
        }

        h1, h2, h3 {
            margin: 0 0 5px;
        }

        section {
            padding: 10px 20px 5px;
            border-bottom: 1px solid black;
        }

        section .header {
            background: red;
            color: #fff;
            font-weight: bold;
            border: 1px solid #000;
            padding: 10px;
        }

        .file {
            background: darkgray;
        }

        .file div {
            display: inline-block;
        }

        .trace {
            background-color: lightgray;
        }

        .log {
            background-color: white;
        }

        dl {
            font-family: monospace;
            font-size: 14px;
            margin: 5px 0 10px;
            border: 1px solid black;
        }

        dt {
            border-bottom: 1px solid black;
            background: black;
            color: white;
            padding: 1%;
            width: 98%;
        }

        dd {
            background: whitesmoke;
            margin: 0;
            overflow-x: auto;
        }

        pre.code {
            line-height: 1.2rem;
            display: inline-block;
            width: 80%;
            margin: 0;
            padding: 5px;
            float: left;
        }

        dd div {
            min-width: 25px;
            display: inline-block;
            line-height: 1.2rem;
            white-space: pre;
            text-align: right;
            padding: 5px;
            float: left;
            background: #fff;
            border-right: 1px #ddd solid;
        }

        dd div span {
            color: red;
        }

        dt span {
            background: red;
            padding: 2px 6px;
        }

        .input,
        .log {
            border: 0;
        }

        .log {
            border-top: 1px solid black;
        }

        table {
            border: 1px black solid;
            border-spacing: 0;
            width: 100%;
            margin: 5px 0 10px;
        }

        .log table {
            border-top: 0;
        }

        th {
            border-right: 1px black solid;
        }

        th, td {
            border-top: 1px black solid;
            padding: 3px;
        }

        tr:hover {
            background: lightgray;
        }

        thead th {
            text-align: left;
            background: black;
            color: white;
            font-size: 110%;
        }

        tbody th {
            text-align: right;
            background: darkgray;
            min-width: 40%;
        }
    </style>
</head>
<body>
<header>
    <small><?= $handler->getLanguage()->render('debug', 'exception') ?>:</small>
    <h1><?= $exception::class ?></h1>
    <small><?= $handler->getLanguage()->render('debug', 'message') ?>:</small>
    <h2><?= htmlentities($exception->getMessage()) ?></h2>
</header>
<section class="file">
    <div>
        <small><?= $handler->getLanguage()->render('debug', 'file') ?>:</small>
        <h3><?= htmlentities($exception->getFile()) ?></h3>
    </div>
    <div>
        <small><?= $handler->getLanguage()->render('debug', 'line') ?>:</small>
        <h3><?= $exception->getLine() ?></h3>
    </div>
</section>
<section class="trace">
    <div class="header"><?= $handler->getLanguage()->render('debug', 'trace') ?>:</div>
    <?php
    $traces = $exception->getTrace();
    if ($traces
        && isset($traces[0]['file'])
        && ($traces[0]['file'] !== $exception->getFile()
            || $traces[0]['line'] !== $exception->getLine())
    ) {
        $traces = array_reverse($traces);
        $traces[] = [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ];
        $traces = array_reverse($traces);
    }
    ?>
    <?php foreach ($traces as $key => $trace) : ?>
        <?php if (isset($trace['file'])) : ?>
            <?php if (is_readable($trace['file'])) : ?>
                <dl>
                    <dt>
                        <span><?= count($traces) - $key ?></span>
                        <?= $trace['file'] ?>:<?= $trace['line'] ?>
                    </dt>
                    <dd>
                        <?php
                        $lines = [];
                        $pre = '';
                        $handle = fopen($trace['file'], 'rb');
                        $line = 1;
                        while ($handle && ! feof($handle)) {
                            $code = fgets($handle);
                            if ($line > ($trace['line'] - 10) && $line < ($trace['line'] + 10)) {
                                $pre .= rtrim((string) $code) . \PHP_EOL;
                                $lines[] = $line;
                            }
                            $line++;
                        }
                        if ($handle) {
                            fclose($handle);
                        }
                        ?>
                        <div><?php
                            foreach ($lines as $line) {
                                if ($line === $trace['line']) {
                                    echo '<span>';
                                    echo $line . \PHP_EOL;
                                    echo '</span>';
                                } else {
                                    echo $line . \PHP_EOL;
                                }
                            }
                            ?></div>
                        <pre class="code"><?= htmlentities($pre) ?></pre>
                    </dd>
                </dl>
            <?php else : ?>
                <dl>
                    <dt>
                        <span><?= $key ?></span> File
                        <em><?= $trace['file'] ?></em> is not readable.
                    </dt>
                </dl>
            <?php endif ?>
        <?php endif ?>
    <?php endforeach ?>
</section>
<section class="input">
    <div class="header">Input:</div>
    <?php
    $input = [
        'ENV' => filter_input_array(\INPUT_ENV) ?: [],
        'SERVER' => filter_input_array(\INPUT_SERVER) ?: [],
        'GET' => filter_input_array(\INPUT_GET) ?: [],
        'POST' => filter_input_array(\INPUT_POST) ?: [],
        'COOKIE' => filter_input_array(\INPUT_COOKIE) ?: [],
    ];
    foreach ($input as &$item) {
        ksort($item);
    }
    unset($item);
    ?>

    <?php foreach ($input as $key => $values) : ?>
        <?php
        if (empty($values)) {
            continue;
        }
        ?>
        <table>
            <thead>
            <tr>
                <th colspan="2"><?= $key ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($values as $field => $value) : ?>
                <tr>
                    <th><?= htmlentities($field) ?></th>
                    <td><?= htmlentities(is_array($value) ? print_r($value, true) : $value) ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    <?php endforeach ?>
</section>
<?php if ($handler->getLogger() && ($log = $handler->getLogger()->getLastLog())): ?>
    <section class="log">
        <div class="header">Log:</div>
        <table>
            <tr>
                <th>Filename</th>
                <td><?= htmlentities($log->filename) ?></td>
            </tr>
            <tr>
                <th>Date</th>
                <td><?= htmlentities($log->date) ?></td>
            </tr>
            <tr>
                <th>Time</th>
                <td><?= htmlentities($log->time) ?></td>
            </tr>
            <tr>
                <th>Level Name</th>
                <td><?= htmlentities($log->levelName) ?></td>
            </tr>
            <tr>
                <th>ID</th>
                <td><?= htmlentities($log->id) ?></td>
            </tr>
            <tr>
                <th>Message</th>
                <td style="white-space: pre-wrap"><?= htmlentities($log->message) ?></td>
            </tr>
            <tr>
                <th>Written</th>
                <td><?= $log->written ? 'true' : 'false' ?></td>
            </tr>
        </table>
    </section>
<?php endif ?>
</body>
</html>
