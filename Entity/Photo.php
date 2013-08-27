<?php

/**
* This file is part of the AntewesBadgeBundle package.
*
* (c) antweb <http://github.com/antwebes/>
*
* This source file is subject to the MIT license that is bundled
* with this source code in the file LICENSE.
*/

namespace Ant\PhotoRestBundle\Entity;

use Ant\PhotoRestBundle\Model\Photo as BasePhoto;

/**
* Default ORM implementation of BadgetInterface.
*
* Must be extended and properly mapped by the end developer.
*
* @author Pablo  <pablo@antweb.es>
*/
abstract class Photo extends BasePhoto
{
	public function __toString()
	{
		return $this->getTitle();
	}
 
	
}