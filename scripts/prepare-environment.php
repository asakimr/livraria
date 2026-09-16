<?php

// Prepara o ambiente sem trocar a chave de uma instalação existente.
$root = dirname(__DIR__);
chdir($root);

if (! file_exists('.env') && ! copy('.env.example', '.env')) {
    throw new RuntimeException('Não foi possível criar o arquivo .env.');
}

$contents = file_get_contents('.env');

if ($contents === false) {
    throw new RuntimeException('Não foi possível ler o arquivo .env.');
}

preg_match('/^APP_KEY=(.*)$/m', $contents, $matches);
$key = trim($matches[1] ?? '', " \t\n\r\0\x0B\"'");

if ($key === '') {
    passthru(escapeshellarg(PHP_BINARY).' artisan key:generate --force', $status);

    if ($status !== 0) {
        exit($status);
    }
}

// Caminho padrão do SQLite local. A configuração MySQL não usa este arquivo.
if (! file_exists('database/database.sqlite') && ! touch('database/database.sqlite')) {
    throw new RuntimeException('Não foi possível criar o arquivo SQLite.');
}

echo "Ambiente preparado; chave existente preservada.\n";
