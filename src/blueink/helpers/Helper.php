<?php
namespace Blueink\ClientSDK;

class Helper
{
	/**
	 * Generate key for creating object
	 * 
	 * @param string $type
	 * @param int $length 
	 * 
	 * @return string $key
	 */
	public static function generate_key(string $type, int $length = 5)
	{
		$ascii_lowercase = 'abcdefghijklmnopqrstuvwxyz';
		$ascii_uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$ascii_letters = $ascii_lowercase . $ascii_uppercase;
		$slug = substr(str_shuffle($ascii_letters), 1, $length);

		return $type . "_" . $slug;
	}
	/**
	 * Merge current array data with additional array data before create class object
	 * 
	 * @param array $data
	 * @param array $additional_data
	 * 
	 * @return array array after merge
	 */
	public static function merge_additional_data(?array $data = [], ?array $additional_data = [])
	{
		if (!is_array($data) || !is_array($additional_data))
			throw new \ErrorException("Merge Error! Data is not array \n\n");

		if (!is_null($additional_data)) {
			$data = array_merge($data, $additional_data);
		}

		return $data;
	}
	/**
	 * Remove null properties from object
	 * 
	 * @param object $object: Object
	 * 
	 * @return mixed Object after remove null properties
	 */
	public static function remove_null_properties(?object $object) {
		if (is_null($object)) {
			return null;
		}
		
		return (object) array_filter((array) $object);
	}
}
