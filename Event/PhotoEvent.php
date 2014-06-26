<?php

namespace Ant\PhotoRestBundle\Event;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\EventDispatcher\Event;

class PhotoEvent extends Event
{
    
    private $photo;

    public function __construct($photo)
    {
    	$this->photo = $photo;
    }

    /**
     * Returns the path of the photo
     *
     * @return string
     */
    public function getPhoto()
    {
    	return $this->photo;
    }
}
