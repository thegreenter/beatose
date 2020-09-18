<?php

class DoctrineMigrations
{
    public static function run()
    {
        if (empty(getenv('CI'))) {
            return;
        }

        $baseProject = __DIR__.'/../';
        echo 'Create Database...'.PHP_EOL;
        shell_exec('php '.$baseProject.'bin/console doctrine:database:create');
        echo 'Run Database Migrations...'.PHP_EOL;
        shell_exec('php '.$baseProject.'bin/console doctrine:migrations:migrate --no-interaction');
    }
}

DoctrineMigrations::run();