<?php

namespace Ant\PhotoRestBundle\ModelManager;

use Ant\PhotoRestBundle\Model\ParticipantInterface;
use Ant\PhotoRestBundle\Model\PhotoInterface;
use Ant\PhotoRestBundle\Model\AlbumInterface;

use Gaufrette\Filesystem;

/**
 * With this class you can create a entity Photo, without class final and independent of ORM
 * @author Chrysweel
 *
 */

abstract class PhotoManager implements PhotoManagerInterface
{
	/**
	 * @var Filesystem
	 */
	protected $fileSystem;

	public function __construct(Filesystem $fileSystem)
	{
		$this->fileSystem = $fileSystem;
	}

	public function savePhoto(PhotoInterface $photo)
	{
		$this->doSavePhoto($photo);
	}
	
	public function deletePhoto(PhotoInterface $photo)
	{
		$this->deleteFile($photo->getPath());
		$this->doDeletePhoto($photo);
	}

	public function deleteFile($path)
	{
		if (!$this->fileSystem->has($path)){
			throw new \InvalidArgumentException("The path of photo does not exist and was not deleted");
		}
		$this->fileSystem->delete($path);

	}
	
	public function createPhoto()
	{
		$class = $this->getClass();
		$photo = new $class;
		
		return $photo;
	}
	/**
	 * @param string $id
	 * @return PhotoInterface
	 */
	public function findPhotoById($id)
	{
		return $this->findOnePhotoBy(array('id' => $id));
	}
	/**
	 * get all photos of an user
	 */
	public function findAllMePhotos(ParticipantInterface $participant)
	{
		return $this->findPhotoBy(array('participant' => $participant));
	}
	/**
	 * calculate the new score of a photo with the new vote
	 * @param PhotoInterface $photo
	 * @param integer $newPhoto
	 */
	public function updateScore(PhotoInterface $photo, $newVote, $operator)
	{
		$scoreOld = $photo->getScore();
		
		$votesOld = $photo->getNumberVotes();

		if ($operator == '+'){
			$newScore = ($votesOld * $scoreOld + $newVote) / ($votesOld + 1);
			$photo->setScore($newScore);
			$photo->setNumberVotes($votesOld + 1);
		}
		else if ($operator == '-'){
			
			if ( ($votesOld - 1) != 0 ){
				$newScore = ($votesOld * $scoreOld - $newVote) / ($votesOld - 1) ;
			} else $newScore = null;
			$photo->setScore($newScore);
			$photo->setNumberVotes($votesOld - 1);
		}
		
		$this->doSavePhoto($photo);
	}
	
	public function isOwner(ParticipantInterface $user, PhotoInterface $photo)
	{
		return ($user->getId() == $photo->getParticipant()->getId());
	}
	
	public function insertToAlbum(PhotoInterface $photo, AlbumInterface $album)
	{
// 		$photo->setAlbum($album);
		$album->addPhoto($photo);
		
		$this->doSavePhoto($photo);		
	}
	
	public function deleteOfAlbum(PhotoInterface $photo, AlbumInterface $album)
	{
		$album->getPhotos()->removeElement($photo);
		$photo->setAlbum(null);
		
		$this->doSavePhoto($photo);
	}
}