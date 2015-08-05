<!DOCTYPE html>
<html>
<head>
    <title>PhileCMS Development Error Handler</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="<?= $base_url ?>/plugins/phile/errorHandler/base.css" type="text/css" rel="stylesheet" />
</head>
<body>
<header><img src="<?= $base_url ?>/plugins/phile/errorHandler/phile-icon.png" title="PhileCMS" alt="Phile Logo"/></header>
<div class="main">
    <div class="header">PhileCMS Development Error Handler</div>
    <div class="body">
        <h2><?= $type ?></h2>
        <p>
            <strong class="red"><?= $exception_message ?> [<?= $exception_code ?>]</strong><?= $wiki_link ?>
			<br />
            <span class="exception"><?= $exception_class ?></span>
			triggered in file <span class="file"><?= $exception_file ?></span> on line <span class="line"><?= $exception_line ?></span>.
        </p>
        <?= $exception_fragment ?>
		<?php if (isset($exception_backtrace)): ?>
			<h2>Backtrace</h2>
			<?= $exception_backtrace ?>
		<?php endif; ?>
    </div>
</div>
</body>
</html>
