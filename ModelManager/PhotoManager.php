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
	
	public function createPhoto()
	{
		$class = $this->getClass();
		$photo = new $class;
		
		return $photo;
	}
}