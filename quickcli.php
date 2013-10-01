<?php

namespace QuickCLI;

/**
 * Simple helper class for quick CLI scripts
 * @author Pekka S. <nospam@astudios.org>
 * @link https://github.com/ThePaavero/QuickCLI
 */
class QuickCLI {

	/**
	 * Application name
	 * @var string
	 */
	private $app = 'Unknown application';

	/**
	 * Constructor
	 * @param string $name e.g. 'My CLI application'
	 */
	public function __construct($name = '')
	{
		// Make sure we're not called over HTTP
		(php_sapi_name() === 'cli' and STDIN) or exit;

		// Assign application name
		$this->app = ! empty($name) ? $name : $this->app;

		// We'll possibly need a polyfill for readline()
		$this->assureReadline();
	}

	/**
	 * Get application name
	 * @return string
	 */
	public function getAppName()
	{
		return $this->app;
	}

	/**
	 * Write line to console
	 * @param  string  $string         e.g. 'App running...'
	 * @param  integer $newline_amount
	 * @param  string  $foreground     e.g. 'green'
	 * @param  string  $background     e.g. 'red'
	 * @return void
	 */
	public function line($string, $newline_amount = 1, $foreground = 'white', $background = 'black')
	{
		// Default postfix to empty string
		$postfix = '';

		// Append EOLs to our string
		while($newline_amount > 0)
		{
			$postfix .= PHP_EOL;
			$newline_amount --;
		}

		// Unless we're on WIN, colorize our string
		if( ! $this->isWindows())
		{
			$string = $this->bash_color($string, $foreground, $background);
		}

		// Output with postfix
		echo $string . $postfix;
	}

	/**
	 * Is our OS Windows?
	 * @return boolean
	 */
	public function isWindows()
	{
		return strstr(php_uname(), 'Windows');
	}

	/**
	 * Prompt user
	 * @param  string  $prompt    e.g. 'Username'
	 * @param  boolean $mandatory Keep asking until we get an answer?
	 * @return string
	 */
	public function prompt($prompt, $mandatory = false)
	{
		$postfix = ': ';
		$prompt .= ': ';

		if($this->isWindows())
		{
			// Windows has its own logic for this
			echo $prompt;
			$line = stream_get_line(STDIN, 1024, PHP_EOL);
		}
		else
		{
			// Unix etc.
			$line = readline($prompt);
		}

		// If this is mandatory input, keep asking until we get it
		if(trim($line) === '' and $mandatory === true)
		{
			echo 'Mandatory input, please provide.' . PHP_EOL;
			return $this->prompt(str_replace($postfix, '', $prompt), $mandatory);
		}

		return $line;
	}

	/**
	 * Colorize string
	 *
	 * @copyright 2011 Arkadius Stefanski (MIT License)
	 * @author    Arkadius Stefanski <arkste[at]gmail[dot]com>
	 */
	private function bash_color($string, $color='white', $background='black')
	{
		$colored_string = '';

		$_color['black']         = '0;30';
		$_color['dark_gray']     = '1;30';
		$_color['blue']          = '0;34';
		$_color['light_blue']    = '1;34';
		$_color['green']         = '0;32';
		$_color['light_green']   = '1;32';
		$_color['cyan']          = '0;36';
		$_color['light_cyan']    = '1;36';
		$_color['red']           = '0;31';
		$_color['light_red']     = '1;31';
		$_color['purple']        = '0;35';
		$_color['light_purple']  = '1;35';
		$_color['brown']         = '0;33';
		$_color['yellow']        = '1;33';
		$_color['light_gray']    = '0;37';
		$_color['white']         = '1;37';

		$_bg_color['black']      = '40';
		$_bg_color['red']        = '41';
		$_bg_color['green']      = '42';
		$_bg_color['yellow']     = '43';
		$_bg_color['blue']       = '44';
		$_bg_color['magenta']    = '45';
		$_bg_color['cyan']       = '46';
		$_bg_color['light_gray'] = '47';

		if(isset($_color[$color]))
		{
			$colored_string .= "\033[" . $_color[$color] . "m";
		}

		if(isset($_bg_color[$background]))
		{
			$colored_string .= "\033[" . $_bg_color[$background] . "m";
		}

		$colored_string .= $string . "\033[0m";

		return $colored_string;
	}

	/**
	 * Load up a polyfill for readline() if needed
	 * @return void
	 */
	private function assureReadline()
	{
		if( ! function_exists('readline'))
		{
			function readline($prompt = '')
			{
			    echo $prompt;
			    return rtrim( fgets( STDIN ), "\n" );
			}
		}
	}

}
