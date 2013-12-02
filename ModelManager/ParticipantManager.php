<?php

namespace Ant\PhotoRestBundle\ModelManager;

use Ant\PhotoRestBundle\Model\ParticipantInterface;

/**
 * With this class you can create a entity Participant, without class final and independent of ORM
 * @author Chrysweel
 *
 */

abstract class ParticipantManager
{

	/**
	 * @param string $id
	 * @return ParticipantInterface
	 */
	public function findParticipantById($id)
	{
		return $this->findParticipantBy(array('id' => $id));
	}

}