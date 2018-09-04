#!/usr/bin/env php
<?php
umask(0000);

require_once __DIR__.'/../vendor/autoload.php';

set_time_limit(0);

use Symfony\Component\Console\Input\ArgvInput;

$input = new ArgvInput();
$env = $input->getParameterOption(array('--env', '-e'), getenv('SYMFONY_ENV') ?: 'dev');

$app = require __DIR__.'/../src/startup.php';
require __DIR__.'/../config/'.$env.'.php';
$console = require __DIR__.'/../src/console.php';
$console->run();