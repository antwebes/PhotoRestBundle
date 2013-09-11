<?php

namespace Ant\PhotoRestBundle\Model;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

abstract class Album implements AlbumInterface
{	
	protected $id;
	
	protected $participant;
	
	protected $title;
	
	protected $description;
	
	/**
	 * @see Ant\PhotoRestBundle\Model\FotoInterface::setParticipant()
	 */
	public function setParticipant(ParticipantInterface $participant)
	{
		$this->participant = $participant;
	}
	/**
	 * @see ant\PhotoRestBundle\Model\FotoInterface::getParticipant()
	 */
	public function getParticipant()
	{
		return $this->participant;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function setTitle($title) {
		$this->title = $title;
	}
	public function getDescription() {
		return $this->description;
	}
	public function setDescription($description){
		$this->description = $description;
	}
}