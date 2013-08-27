<?php

namespace Ant\PhotoRestBundle\Model;
use Symfony\Component\HttpFoundation\File\UploadedFile;
interface PhotoInterface {

	public function getTitle();
	
	public function setTitle($title);

	
	public function getImage();

	public function setImage(UploadedFile $image = null);

    

   


    
}