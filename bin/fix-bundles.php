<?php

declare(strict_types=1);

$file = __DIR__.'/../config/bundles.php';
$content = file_get_contents($file);

if (false === $content) {
    exit(0);
}

if (str_contains($content, 'declare(strict_types=1)')) {
    exit(0);
}

$content = str_replace("<?php\n", "<?php\n\ndeclare(strict_types=1);\n", $content);
file_put_contents($file, $content);

echo "Fixed: added declare(strict_types=1) to config/bundles.php\n";
