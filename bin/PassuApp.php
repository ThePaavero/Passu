<?php

/**
 * PassuApp Class
 * @author Pekka S. <nospam@astudios.org>
 * @package Passu
 * @link https://github.com/ThePaavero/Passu
 * @license MIT
 */

namespace PassuApp;

class PassuApp
{
	public function __construct($basedir)
	{
		$this->setBaseDir($basedir);

		$this->paths = array(
			'htaccess' => $this->dir . '.htaccess',
			'htpasswd' => $this->dir . '.htpasswd'
		);
	}

	public function setBaseDir($basedir)
	{

		// Make sure we have a slash in the end
		$basedir = substr($basedir, strlen($basedir)-1) === '/' ? $basedir : $basedir . '/';

		$this->dir = $basedir;
	}

	/**
	 * Do a kind of a dry run to see if there are some obvious problems
	 * @return array
	 */
	public function getPreliminaryProblems()
	{
		$errors = array();

		// Does basedir exist?
		if( ! is_dir($this->dir))
		{
			$errors[] = 'Basedir does not exist ("' . $this->dir . '")';

			// In this case, there's no point in going on
			return $errors;
		}

		// Does .htaccess file exist?
		if(file_exists($this->paths['htaccess']))
		{
			// It does. Do we have permission to write to it?
			if( ! is_writable($this->paths['htaccess']))
			{
				// Nope
				$errors[] = '.htaccess file exists, but is not writable.';
			}
		}
		else
		{
			// It does not exist.
			// Let's try to create one (and delete it right away)
			try
			{
				touch($this->paths['htaccess']);
				unlink($this->paths['htaccess']);
			}
			catch (Exception $e)
			{
				$errors[] = '.htaccess file does not exist, and application cannot create one.';
			}
		}

		// Does .htpasswd file exist?
		if(file_exists($this->paths['htpasswd']))
		{
			// It does. Do we have permission to write to it?
			if( ! is_writable($this->paths['htpasswd']))
			{
				// Nope
				$errors[] = '.htpasswd file exists, but is not writable.';
			}
		}
		else
		{
			// It does not exist.
			// Let's try to create one (and delete it right away)
			try
			{
				touch($this->paths['htpasswd']);
				unlink($this->paths['htpasswd']);
			}
			catch (Exception $e)
			{
				$errors[] = '.htaccess file does not exist, and application cannot create one.';
			}
		}

		return $errors;
	}

	/**
	 * Do our main thing
	 * @param  string $username
	 * @param  string $password
	 * @return null
	 */
	public function generate($username, $password)
	{
		// First, do the .htpasswd file
		$data = $username . ':' . crypt($password) . PHP_EOL;
		file_put_contents($this->paths['htpasswd'], $data, FILE_APPEND);

		// Then, move on to .htaccess
		$tokens = array(
				'[HTPASSWD_PATH]'
			);

		$replacements = array(
				$this->paths['htpasswd']
			);

		$template = file_get_contents(__DIR__ . '/htaccess_template');
		$data = str_replace($tokens, $replacements, $template);

		file_put_contents($this->paths['htaccess'], $data, FILE_APPEND);
	}

	/**
	 * Generate random password
	 * @param  integer $length
	 * @return string
	 */
	public function generateRandomPassword($length = 9)
	{
		$characters        = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789!@.,?+-_";
		$password          = array();
		$characters_length = strlen($characters) - 1;

		for($i = 0; $i < $length; $i ++)
		{
			$n = rand(0, $characters_length);
			$pass[] = $characters[$n];
		}

		return implode($pass);
	}
}