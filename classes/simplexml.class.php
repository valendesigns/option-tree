<?php

/**
 * If the result will be an object, this container class is used.
 *
 */

class SimpleXMLObject{
	function attributes(){
		$container = get_object_vars($this);
		return (object) $container["@attributes"];
	}
	function content(){
		$container = get_object_vars($this);
		return (object) $container["@content"];
	}
}

/**
 * The Main XML Parser Class
 *
 */
class simplexml {
	var $result = array();
	var $ignore_level = 0;
	var $skip_empty_values = false;
	var $php_errormsg;
	var $evalCode="";

	/**
	 * Adds Items to Array
	 *
	 * @param int $level
	 * @param array $tags
	 * @param $value
	 * @param string $type
	 */
	function array_insert($level, $tags, $value, $type)
	{
		$temp = '';
		for ($c = $this->ignore_level + 1; $c < $level + 1; $c++) {
			if (isset($tags[$c]) && (is_numeric(trim($tags[$c])) || trim($tags[$c]))) {
				if (is_numeric($tags[$c])) {
					$temp .= '[' . $tags[$c] . ']';
				} else {
					$temp .= '["' . $tags[$c] . '"]';
				}
			}
		}
		$this->evalCode .= '$this->result' . $temp . "=\"" . addslashes($value) . "\";//(" . $type . ")\n";
		#echo $code. "\n";
	}

	/**
	 * Define the repeated tags in XML file so we can set an index
	 *
	 * @param array $array
	 * @return array
	 */
	function xml_tags($array)
	{	$repeats_temp = array();
	$repeats_count = array();
	$repeats = array();

	if (is_array($array)) {
		$n = count($array) - 1;
		for ($i = 0; $i < $n; $i++) {
			$idn = $array[$i]['tag'].$array[$i]['level'];
			if(in_array($idn,$repeats_temp)){
				$repeats_count[array_search($idn,$repeats_temp)]+=1;
			}else{
				array_push($repeats_temp,$idn);
				$repeats_count[array_search($idn,$repeats_temp)]=1;
			}
		}
	}
	$n = count($repeats_count);
	for($i=0;$i<$n;$i++){
		if($repeats_count[$i]>1){
			array_push($repeats,$repeats_temp[$i]);
		}
	}
	unset($repeats_temp);
	unset($repeats_count);
	return array_unique($repeats);
	}


	/**
	 * Converts Array Variable to Object Variable
	 *
	 * @param array $arg_array
	 * @return $tmp
	 */
	function array2object ($arg_array)
	{

		if (is_array($arg_array)) {
			$keys = array_keys($arg_array);
			if(!is_numeric($keys[0])) $tmp = new SimpleXMLObject;
			foreach ($keys as $key) {
				if (is_numeric($key)) $has_number = true;
				if (is_string($key)) $has_string = true;
			}
			if (isset($has_number) and !isset($has_string)) {
				foreach ($arg_array as $key => $value) {
					$tmp[] = $this->array2object($value);
				}
			} elseif (isset($has_string)) {
				foreach ($arg_array as $key => $value) {
					if (is_string($key))
					$tmp->$key = $this->array2object($value);
				}
			}
		} elseif (is_object($arg_array)) {
			foreach ($arg_array as $key => $value) {
				if (is_array($value) or is_object($value))
				$tmp->$key = $this->array2object($value);
				else
				$tmp->$key = $value;
			}
		} else {
			$tmp = $arg_array;
		}
		return $tmp; //return the object
	}

	/**
	 * Reindexes the whole array with ascending numbers
	 *
	 * @param array $array
	 * @return array
	 */
	function array_reindex($array)
	{
		if (is_array($array)) {
			if(count($array) == 1 && $array[0]){
				return $this->array_reindex($array[0]);
			}else{
				foreach($array as $keys => $items) {
					if (is_array($items)) {
						if (is_numeric($keys)) {
							$array[$keys] = $this->array_reindex($items);
						} else {
							$array[$keys] = $this->array_reindex(array_merge(array(), $items));
						}
					}
				}
			}
		}

		return $array;
	}


	/**
	 * Parse the XML generation to array object
	 *
	 * @param array $array
	 * @return array
	 */
	function xml_reorganize($array)
	{
		$count = count($array);
		$repeat = $this->xml_tags($array);
		$repeatedone = false;
		$tags = array();
		$k = 0;
		for ($i = 0; $i < $count; $i++) {
			switch ($array[$i]['type']) {
				case 'open':
					array_push($tags, $array[$i]['tag']);
					if ($i > 0 && ($array[$i]['tag'] == $array[$i-1]['tag']) && ($array[$i-1]['type'] == 'close'))
					$k++;
					if (isset($array[$i]['value']) && ($array[$i]['value'] || !$this->skip_empty_values)) {
						array_push($tags, '@content');
						$this->array_insert(count($tags), $tags, $array[$i]['value'], "open");
						array_pop($tags);
					}

					if (in_array($array[$i]['tag'] . $array[$i]['level'], $repeat)) {
						if (($repeatedone == $array[$i]['tag'] . $array[$i]['level']) && ($repeatedone)) {
							array_push($tags, strval($k++));
						} else {
							$repeatedone = $array[$i]['tag'] . $array[$i]['level'];
							array_push($tags, strval($k));
						}
					}

					if (isset($array[$i]['attributes']) && $array[$i]['attributes'] && $array[$i]['level'] != $this->ignore_level) {
						array_push($tags, '@attributes');
						foreach ($array[$i]['attributes'] as $attrkey => $attr) {
							array_push($tags, $attrkey);
							$this->array_insert(count($tags), $tags, $attr, "open");
							array_pop($tags);
						}
						array_pop($tags);
					}
					break;

				case 'close':
					array_pop($tags);
					if (in_array($array[$i]['tag'] . $array[$i]['level'], $repeat)) {
						if ($repeatedone == $array[$i]['tag'] . $array[$i]['level']) {
							array_pop($tags);
						} else {
							$repeatedone = $array[$i + 1]['tag'] . $array[$i + 1]['level'];
							array_pop($tags);
						}
					}
					break;

				case 'complete':
					array_push($tags, $array[$i]['tag']);
					if (in_array($array[$i]['tag'] . $array[$i]['level'], $repeat)) {
						if ($repeatedone == $array[$i]['tag'] . $array[$i]['level'] && $repeatedone) {
							array_push($tags, strval($k));
						} else {
							$repeatedone = $array[$i]['tag'] . $array[$i]['level'];
							array_push($tags, strval($k));
						}
					}

					if (isset($array[$i]['value']) && ($array[$i]['value'] || !$this->skip_empty_values)) {
						if (isset($array[$i]['attributes']) && $array[$i]['attributes']) {
							array_push($tags, '@content');
							$this->array_insert(count($tags), $tags, $array[$i]['value'], "complete");
							array_pop($tags);
						} else {
							$this->array_insert(count($tags), $tags, $array[$i]['value'], "complete");
						}
					}

					if (isset($array[$i]['attributes']) && $array[$i]['attributes']) {
						array_push($tags, '@attributes');
						foreach ($array[$i]['attributes'] as $attrkey => $attr) {
							array_push($tags, $attrkey);
							$this->array_insert(count($tags), $tags, $attr, "complete");
							array_pop($tags);
						}
						array_pop($tags);
					}

					if (in_array($array[$i]['tag'] . $array[$i]['level'], $repeat)) {
						array_pop($tags);
						$k++;
					}

					array_pop($tags);
					break;
			}
		}
		eval($this->evalCode);
		$last = $this->array_reindex($this->result);
		return $last;
	}

	/**
	 * Get the XML contents and parse like SimpleXML
	 *
	 * @param string $file
	 * @param string $resulttype
	 * @param string $encoding
	 * @return array/object
	 */
	function xml_load_file($file, $resulttype = 'object', $encoding = 'UTF-8')
	{
		$php_errormsg="";
		$this->result="";
		$this->evalCode="";
		$values="";
		$data = file_get_contents($file);
		if (!$data)
		return 'Cannot open xml document: ' . (isset($php_errormsg) ? $php_errormsg : $file);

		$parser = xml_parser_create($encoding);
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		$ok = xml_parse_into_struct($parser, $data, $values);
		if (!$ok) {
			$errmsg = sprintf("XML parse error %d '%s' at line %d, column %d (byte index %d)",
			xml_get_error_code($parser),
			xml_error_string(xml_get_error_code($parser)),
			xml_get_current_line_number($parser),
			xml_get_current_column_number($parser),
			xml_get_current_byte_index($parser));
		}

		xml_parser_free($parser);
		if (!$ok)
		return $errmsg;
		if ($resulttype == 'array')
		return $this->xml_reorganize($values);
		// default $resulttype is 'object'
		return $this->array2object($this->xml_reorganize($values));
	}
}

?>