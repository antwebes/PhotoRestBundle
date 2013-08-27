<?php

namespace Ant\PhotoRestBundle\Model;

use Doctrine\ORM\Mapping as ORM;

use Ant\PhotoRestBundle\Model\ParticipantInterface;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class Photo implements PhotoInterface {
	
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
	/**
	 * @Assert\File(
	 *     maxSize="222222222222000k",
	 *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg"}
	 * )
	 *
	 * @var File $image
	 */
	public $image;
	
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
	public function getImage() {
		return $this->image;
	}
	
	public function setImage(UploadedFile $image = null) {
		$this->image = $image;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function setTitle($title) {
		$this->title = $title;
	}
}