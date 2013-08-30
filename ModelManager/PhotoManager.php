<?php

namespace Ant\PhotoRestBundle\ModelManager;

use Ant\PhotoRestBundle\Model\PhotoInterface;

/**
 * With this class you can create a entity Photo, without class final and independent of ORM
 * @author Chrysweel
 *
 */

abstract class PhotoManager
{
	public function savePhoto(PhotoInterface $photo)
	{
		$this->doSavePhoto($photo);
	}
	
	public function deletePhoto(PhotoInterface $photo)
	{
		$this->doDeletePhoto($photo);
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
		return $this->findPhotoBy(array('id' => $id));
	}
	
	/**
	 * calculate the new score of a photo with the new vote
	 * @param PhotoInterface $photo
	 * @param integer $newPhoto
	 */
	public function updateScore(PhotoInterface $photo, $newVote, $operator)
	{
		$scoreOld = $photo->getScore();
		$votesOld = $photo->getVotes();
		if ($operator == '+'){
			$newScore = ($votesOld * $scoreOld + $newVote) / ($votesOld + 1);
			$photo->setScore($newScore);
			$photo->setVotes($votesOld + 1);
		}
		else if ($operator == '-'){
			if ( ($votesOld - 1) != 0 ){
				$newScore = ($votesOld * $scoreOld - $newVote) / ($votesOld - 1) ;
			} else $newScore = null;
			$photo->setScore($newScore);
			$photo->setVotes($votesOld - 1);
		}
		$this->doSavePhoto($photo);
	}
}