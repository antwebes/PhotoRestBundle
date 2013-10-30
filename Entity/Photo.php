<?php

/**
* This file is part of the AntewesPhotoRestBundle package.
*
* (c) antweb <http://github.com/antwebes/>
*
* This source file is subject to the MIT license that is bundled
* with this source code in the file LICENSE.
*/

namespace Ant\PhotoRestBundle\Entity;

use Ant\PhotoRestBundle\Model\Photo as BasePhoto;
use Doctrine\ORM\Mapping as ORM;
/**
*
* Must be extended and properly mapped by the end developer.
*
* @author Pablo  <pablo@antweb.es>
*/
abstract class Photo extends BasePhoto
{
	/**
	 * @ORM\ManyToOne(targetEntity="Album")
	 * @ORM\JoinColumn(name="album_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $album;
	
	public function __toString()
	{
		return $this->getPath();
	}
 
	
}