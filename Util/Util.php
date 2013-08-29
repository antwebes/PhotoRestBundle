<?php 

namespace Ant\PhotoRestBundle\Util;

class Util
{
	public static function getAllFormErrorMessages($form)
	{
		$retval = array();
		foreach ($form->getErrors() as $key => $error) {
			$retval['message'] = $error->getMessage();
		}
		foreach ($form->all() as $name => $child) {
			$errors = Util::getAllFormErrorMessages($child);
			if (!empty($errors)) {
				$retval[$name] = $errors;
			}
		}
		return $retval;
	}
}