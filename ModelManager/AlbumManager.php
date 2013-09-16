<?php

namespace Ant\PhotoRestBundle\ModelManager;

use Ant\PhotoRestBundle\Model\AlbumInterface;
/**
 * With this class you can create a entity Vote, without class final and independent of ORM
 * @author Chrysweel
 *
 */

abstract class AlbumManager
{
	public function save(AlbumInterface $album)
	{
		$this->doSaveAlbum($album);
	}
	
	public function delete(AlbumInterface $album)
	{
		$this->doDelete($album);
	}
	
	public function createAlbum()
	{
		$class = $this->getClass();
		$album = new $class;
		
		return $album;
	}
	
	/**
	 * @param string $id
	 * @return AlbumInterface
	 */
	public function findAlbumById($id)
	{
		return $this->findOneAlbumBy(array('id' => $id));
	}
	
	public function isOwner ($user, AlbumInterface $album)
	{
		return ($user->getId() == $album->getParticipant()->getId());
	}
}