<?php

namespace Ant\PhotoRestBundle\Model;

use Ant\PhotoRestBundle\Model\VoteInterface;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

abstract class Vote implements VoteInterface {
		
	/**
	 * Participant that created the vote
	 *
	 * @var ParticipantInterface
	 */
	protected $participant;
	
	/**
	 * Photo that voted an user
	 *
	 * @var PhotoInterface
	 */
	protected $photo;
	
	/**
	 * @ORM\Column(type="datetime")
	 *
	 * @Assert\Date
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
	
	public function __construct()
	{
		$this->publicatedAt = new \DateTime('now');
	}
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
	/**
	 * @see Ant\PhotoRestBundle\Model\PhotoInterface::setPhoto()
	 */
	public function setPhoto(PhotoInterface $photo)
	{
		$this->photo = $photo;
	}
	/**
	 * @see ant\PhotoRestBundle\Model\PhotoInterface::getPhoto()
	 */
	public function getPhoto()
	{
		return $this->photo;
	}
	
	public function getScore()
	{
		return $this->score;
	}
	public function setScore($score)
	{
		$this->score = $score;
	}
}