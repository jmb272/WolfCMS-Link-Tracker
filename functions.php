<?php

/* Security measure */
if (!defined('IN_CMS')) { exit; }

/**
 * Crops a string after certain length.
 *
 * @param string $string
 * @param int $max_length
 * @param bool $append_hellip Append ... to the string if cropped.
 * @return string
 */
if (!function_exists('crop_text'))
{
	function crop_text($string, $max_length, $append_hellip=true) 
	{
		if (strlen($string) > $max_length) {
			$string  = substr($string, 0, $max_length);
			if ($append_hellip) {
				$string .= '&hellip;';
			}
		}
		
		return $string;
	}
}

/**
 * Checks if $string begins with $trail
 *
 * @param string $string
 * @param string $prefix
 * @param bool $caseSensitive
 * @return bool
 */
if (!function_exists('has_prefix'))
{
	function has_prefix($string, $prefix, $caseSensitive=false)
	{
		if (!$caseSensitive) {
			$string = strtolower($string);
			$prefix = strtolower($prefix);
		}
		
		$sLen = strlen($string);
		$pLen = strlen($prefix);
		
		if ($pLen > $sLen) { return false; }
		
		return (substr($string, 0, $pLen) == $prefix);
	}
}

/**
 * Prefix $prefix to $string if its not there already.
 *
 * @param string $string
 * @param string $prefix
 * @param bool $caseSensitive
 * @return string
 */
if (!function_exists('add_prefix'))
{
	function add_prefix($string, $prefix, $caseSensitive=false)
	{
		if (!has_prefix($string, $prefix, $caseSensitive)) {
			$string = $prefix . $string;
		}
		
		return $string;
	}
}

/**
 * Removes $prefix from the beginning of $string.
 *
 * @param string $string
 * @param string $prefix
 * @param bool $caseSensitive
 * @return string
 */
if (!function_exists('remove_prefix'))
{
	function remove_prefix($string, $prefix, $caseSensitive=false)
	{
		if (has_prefix($string, $prefix, $caseSensitive)) 
		{
			$sLen = strlen($string);
			$pLen = strlen($prefix);
			
			$string = substr($string, $pLen, ($sLen - $pLen));
		}
		
		return $string;
	}
}

/**
 * Checks if $string ends with $trail.
 *
 * @param string $string
 * @param string $trail
 * @param bool $caseSensitive
 * @return bool
 */
if (!function_exists('has_trail'))
{
	function has_trail($string, $trail, $caseSensitive=false)
	{
		if (!$caseSensitive) {
			$string = strtolower($string);
			$trail = strtolower($trail);
		}
		
		$sLen = strlen($string);
		$tLen = strlen($trail);
		
		if ($tLen > $sLen) { return false; }
		
		return (substr($string, ($sLen - $tLen), $tLen) == $trail);
	}
}

/**
 * Append $trail to $string if its not there already.
 *
 * @param string $string
 * @param string $trail
 * @param bool $caseSensitive
 * @return string
 */
if (!function_exists('add_trail'))
{
	function add_trail($string, $trail, $caseSensitive=false)
	{
		if (!has_trail($string, $trail, $caseSensitive)) {
			$string .= $trail;
		}
		
		return $string;
	}
}

/**
 * Removes $trail from the end of $string.
 *
 * @param string $string
 * @param string $trail
 * @param bool $caseSensitive
 * @return string
 */
if (!function_exists('remove_trail'))
{
	function remove_trail($string, $trail, $caseSensitive=false)
	{
		if (has_trail($string, $trail, $caseSensitive)) 
		{
			$sLen = strlen($string);
			$tLen = strlen($trail);
			
			$string = substr($string, 0, ($sLen - $tLen));
		}
		
		return $string;
	}
}

/**
 * Remove $prefix_trail from the start and end of $string if not there.
 *
 * @param string $string
 * @param string $prefix_trail
 * @param bool $caseSensitive
 * @return string
 */
if (!function_exists('remove_prefix_trail'))
{
	function remove_prefix_trail($string, $prefix_trail, $caseSensitive=false)
	{
		$string = remove_prefix($string, $prefix_trail, $caseSensitive);
		$string = remove_trail($string, $prefix_trail, $caseSensitive);

		return $string;
	}
}

/**
 * Add $prefix_trail to the start and end of $string if not there.
 *
 * @param $string
 * @param $prefix_trail
 * @param bool $caseSensitive
 * @return string
 */
if (!function_exists('add_prefix_trail'))
{
	function add_prefix_trail($string, $prefix_trail, $caseSensitive=false)
	{
		$string = add_prefix($string, $prefix_trail, $caseSensitive);
		$string = add_trail($string, $prefix_trail, $caseSensitive);

		return $string;
	}
}
