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
	 * Participant that created the photo
	 *
	 * @var ParticipantInterface
	 */
	protected $participant;
	
	/**
	 * relation with Entity Vote, array of votes 
	 * @var unknown
	 */
	protected $votes;
	
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
	 * @ORM\Column(type="string")
	 */
	protected $path;
	/**
	 * @ORM\Column(type="string", length=255, name="title", nullable=true)
	 */
	protected $title;
	/**
	 * @Assert\File(
	 *     maxSize="2000k",
	 *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg", "application/octet-stream"}
	 * )
	 *
	 * @var File $image
	 */
	public $image;
	/**
	 * number of voter to this photo
	 * @var int
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $numberVotes=0;
	/**
	 * score of the photo
	 * @var float
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $score=null;
	
	protected $album;
	
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
	public function getPath() {
		return $this->path;
	}
	
	public function setPath($path) {
		$this->path = $path;
	}
	public function getScore() {
		return $this->score;
	}
	
	public function setScore($score) {
		$this->score = $score;
	}
	public function getNumberVotes() {
		return $this->numberVotes;
	}
	
	public function setNumberVotes($numberVotes) {
		$this->numberVotes = $numberVotes;
	}
	
	public function setAlbum($album)
	{
		$this->album = $album;
	}
	
	public function getAlbum()
	{
		return $this->album;
	}
}