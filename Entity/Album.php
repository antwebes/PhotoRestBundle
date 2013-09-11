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

use Ant\PhotoRestBundle\Model\Album as BaseAlbum;
use Doctrine\ORM\Mapping as ORM;
/**
*
* Must be extended and properly mapped by the end developer.
*
* @author Pablo  <pablo@antweb.es>
*/
abstract class Album extends BaseAlbum
{
	/**
	 * @ORM\Column(type="string", length=255, name="title", nullable=true)
	 */
	protected $title;
	
	/**
	 * @ORM\Column(type="string", length=255, name="description", nullable=true)
	 */
	protected $description;
	
	/**
	 * @ORM\OneToMany(targetEntity="Photo", mappedBy="album")
	 */
	private $photos;
	
	public function __toString()
	{
		return $this->getTitle();
	}
 
	
}