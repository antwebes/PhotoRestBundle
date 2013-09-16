<?php

/**
* This file is part of the AntewesPhotoBundle package.
*
* (c) antweb <http://github.com/antwebes/>
*
* This source file is subject to the MIT license that is bundled
* with this source code in the file LICENSE.
*/

namespace Ant\PhotoRestBundle\Entity;

use Ant\PhotoRestBundle\Model\Vote as BaseVote;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Groups;


/**
*
* Must be extended and properly mapped by the end developer.
*
* @author Pablo  <pablo@antweb.es>
*/
abstract class Vote extends BaseVote
{ 
	/**
	 * @ORM\Column(type="datetime")
	 * @Assert\Date
	 * 
	 */
	protected $publicatedAt;
	/**
	 * @ORM\Column(type="float")
	 * @Assert\Range(
	 * 		min = 1,
	 * 		max = 10
	 * ) 
	 */
	protected $score;
}