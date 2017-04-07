<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * active_dropdown_helper.php
 *
 * Contains helper functon(s) to use the active dropdown.
 *
 *
 * NOTES
 *
 * REVISION HISTORY
 * JR (04/03/2013) - Initial version
 *
 */
 
 if(!function_exists('sortByOrder')) {
	function sortByOrder($a, $b) {
		return $a['ordering'] - $b['ordering'];
	}

 }

if ( ! function_exists('singularize'))
{
	function singularize ($params)
	{
		if (is_string($params))
		{
			$word = $params;
		} else if (!$word = $params['word']) {
			return false;
		}

		$singular = array (
			'/(quiz)zes$/i' => '\\1',
			'/(matr)ices$/i' => '\\1ix',
			'/(vert|ind)ices$/i' => '\\1ex',
			'/^(ox)en/i' => '\\1',
			'/(alias|status)es$/i' => '\\1',
			'/([octop|vir])i$/i' => '\\1us',
			'/(cris|ax|test)es$/i' => '\\1is',
			'/(shoe)s$/i' => '\\1',
			'/(o)es$/i' => '\\1',
			'/(bus)es$/i' => '\\1',
			'/([m|l])ice$/i' => '\\1ouse',
			'/(x|ch|ss|sh)es$/i' => '\\1',
			'/(m)ovies$/i' => '\\1ovie',
			'/(s)eries$/i' => '\\1eries',
			'/([^aeiouy]|qu)ies$/i' => '\\1y',
			'/([lr])ves$/i' => '\\1f',
			'/(tive)s$/i' => '\\1',
			'/(hive)s$/i' => '\\1',
			'/([^f])ves$/i' => '\\1fe',
			'/(^analy)ses$/i' => '\\1sis',
			'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\\1\\2sis',
			'/([ti])a$/i' => '\\1um',
			'/(n)ews$/i' => '\\1ews',
			'/s$/i' => ''
		);

		$irregular = array(
			'person' => 'people',
			'man' => 'men',
			'child' => 'children',
			'sex' => 'sexes',
			'move' => 'moves'
		);	

		$ignore = array(
			'equipment',
			'information',
			'rice',
			'money',
			'species',
			'series',
			'fish',
			'sheep',
			'press',
			'sms',
		);

		$lower_word = strtolower($word);
		foreach ($ignore as $ignore_word)
		{
			if (substr($lower_word, (-1 * strlen($ignore_word))) == $ignore_word)
			{
				return $word;
			}
		}

		foreach ($irregular as $singular_word => $plural_word)
		{
			if (preg_match('/('.$plural_word.')$/i', $word, $arr))
			{
				return preg_replace('/('.$plural_word.')$/i', substr($arr[0],0,1).substr($singular_word,1), $word);
			}
		}

		foreach ($singular as $rule => $replacement)
		{
			if (preg_match($rule, $word))
			{
				return preg_replace($rule, $replacement, $word);
			}
		}

		return $word;
	}
}

/* End of file singularize.php */
/* Location: FRAMEWORK/helpers/singularize.php */
