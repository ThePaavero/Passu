<?php

/**
 * passu CLI script
 * @author Pekka S. <nospam@astudios.org>
 * @package Passu
 * @link https://github.com/ThePaavero/Passu
 * @license MIT
 */

require 'bin/quickcli.php';
require 'bin/PassuApp.php';

$cli = new QuickCLI\QuickCLI('Passu');

$cli->line('Welcome to ' . $cli->getAppName(), 2, 'light_cyan');

$basepath = $cli->prompt('Enter project basepath (e.g. /var/www/project/x/)', true);
$username = $cli->prompt('Enter username', true);
$password = $cli->prompt('Enter password (leave empty for random)', false);

$passu = new PassuApp\PassuApp($basepath);

if(empty($password))
{
	$password = $passu->generateRandomPassword(10);
}

$errors = $passu->getPreliminaryProblems();

if( ! empty($errors))
{
	$cli->line('Passu cannot operate, below is a list of errors:', 1, 'red');
	$cli->line(implode(PHP_EOL, $errors));
	exit;
}

$passu->generate($username, $password);

$cli->line('Done.', 1, 'green');
$cli->line('Username: ' . $username);
$cli->line('Password: ' . $password, 2);
