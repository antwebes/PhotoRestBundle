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
	
	public function deleteAlbum(AlbumInterface $album)
	{
		$this->doDeleteVote($album);
	}
	
	public function createAlbum()
	{
		$class = $this->getClass();
		$album = new $class;
		
		return $album;
	}
	
	/**
	 * @param string $id
	 * @return VoteInterface
	 */
	public function findAlbumById($id)
	{
		return $this->findOneAlbumBy(array('id' => $id));
	}
	
}