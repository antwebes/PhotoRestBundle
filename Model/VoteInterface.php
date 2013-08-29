<?php

namespace Ant\PhotoRestBundle\Model;

use Ant\PhotoRestBundle\Model\FotoInterface;

/**
 *
 * @author Pablo <pablo@antweb.es>
 */
interface VoteInterface
{
/**
	 * @see Ant\PhotoRestBundle\Model\FotoInterface::setParticipant()
	 */
	public function setParticipant(ParticipantInterface $participant);
	/**
	 * @see Ant\PhotoRestBundle\Model\FotoInterface::getParticipant()
	 */
	public function getParticipant();
	/**
	 * @see Ant\PhotoRestBundle\Model\PhotoInterface::setPhoto()
	 */
	public function setPhoto(PhotoInterface $photo);
	/**
	 * @see Ant\PhotoRestBundle\Model\PhotoInterface::getPhoto()
	 */
	public function getPhoto();
}
