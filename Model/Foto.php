<?php

namespace Ant\PhotoRestBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Ant\PhotoRestBundle\Model\ParticipantInterface;

abstract class Foto implements FotoInterface {
	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * Participant that created the badge
	 *
	 * @var ParticipantInterface
	 */
	protected $participant;
	
	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 *
	 * @Assert\DateTime
	 */
	protected $updatedAt;
	
	/**
	 * @ORM\Column(type="datetime")
	 *
	 * @Assert\Date
	 */
	protected $publicatedAt;
	/**
	 * @ORM\Column(type="string", length=255, name="title", nullable=true)
	 */
	protected $title;
	
	public function __construct()
	{
		$this->publicatedAt = new \DateTime('now');
	}
	/**
	 * @see Ant\BadgeBundle\Model\FotoInterface::setParticipant()
	 */
	public function setParticipant(ParticipantInterface $participant)
	{
		$this->participant = $participant;
	}
	/**
	 * @see ant\BadgeBundle\Model\FotoInterface::getParticipant()
	 */
	public function getParticipant()
	{
		return $this->participant;
	}
}