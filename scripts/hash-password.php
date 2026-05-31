<?php
declare(strict_types=1);

if ($argc < 2) {
    fwrite(STDERR, "Usage: php scripts/hash-password.php <password>\n");
    exit(1);
}

$password = $argv[1];
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
echo $hash . "\n";
