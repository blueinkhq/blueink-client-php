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
	public static function generateKey(string $type, int $length = 5)
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
	public static function mergeAdditionalData(?array $data = [], ?array $additional_data = [])
	{
		$data = $data ?? [];
		if (is_array($additional_data) && $additional_data !== []) {
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
	public static function removeNullProperties(?object $object) {
		if (is_null($object) && !is_object($object)) {
			return null;
		}

		return (object) array_filter((array) $object);
	}

	/**
	 * Recursively serialize a model graph (objects, nested arrays, scalars)
	 * to an associative-array shape suitable for JSON encoding. Strips null
	 * values and empty arrays at every level. Mirrors the Python SDK's
	 * `dict(exclude_unset=True, exclude_none=True)`.
	 */
	public static function modelToArray(mixed $value): mixed
	{
		if (is_object($value)) {
			$out = [];
			foreach ((array) $value as $key => $child) {
				if (is_string($key) && ($key === '' || $key[0] === "\0")) {
					continue;
				}
				$converted = self::modelToArray($child);
				if (self::isPresent($converted)) {
					$out[$key] = $converted;
				}
			}

			return $out;
		}

		if (is_array($value)) {
			$is_list = array_is_list($value);
			$out = [];
			foreach ($value as $key => $child) {
				$converted = self::modelToArray($child);
				if (!self::isPresent($converted)) {
					continue;
				}
				if ($is_list) {
					$out[] = $converted;
				} else {
					$out[$key] = $converted;
				}
			}

			return $out;
		}

		return $value;
	}

	private static function isPresent(mixed $value): bool
	{
		if (is_null($value)) {
			return false;
		}
		if (is_array($value) && $value === []) {
			return false;
		}

		return true;
	}
}
