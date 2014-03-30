<!DOCTYPE html>
<html>
    <head>
        <title>Error</title>
    </head>
    <body>
        <?php
            /** @var Headzoo\Core\ErrorHandler $handler */
            $exception = $handler->getLastError();
        ?>
        <h1><?=htmlspecialchars($exception->getMessage())?></h1>
        <p>
            <?=nl2br(htmlspecialchars($exception->getTraceAsString()))?>
        </p>
    </body>
</html>