<?php 

namespace Ant\PhotoRestBundle\Util;

use Ant\PhotoRestBundle\Entity\Error;

class ErrorResponse
{	
	public static function createResponse($error, $code){
		
		$r = new Error();
		$r->setErrors($error);
		$r->setCode($code);
		
		return $r;
	}
	
}