<?php

namespace Ant\PhotoRestBundle\ModelManager;

use Ant\PhotoRestBundle\Model\PhotoInterface;

/**
 * @author pc
 */
interface PhotoManagerInterface
{
	public function savePhoto(PhotoInterface $photo);
	
	public function createPhoto();
	
	public function deletePhoto(PhotoInterface $photo);

    /**
     * @param string $id
     * @return PhotoInterface
     */
    public function findPhotoById($id);
}