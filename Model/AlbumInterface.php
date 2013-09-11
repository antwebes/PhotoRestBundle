<?php

namespace Ant\PhotoRestBundle\Model;

interface AlbumInterface {

	public function getTitle();
	
	public function setTitle($title);

	
	public function getDescription();

	public function setDescription($description);

}