<?php

namespace Ant\PhotoRestBundle\Event;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\EventDispatcher\Event;

class PhotoEvent extends Event
{
    
    private $path;

    public function __construct($path)
    {
    	$this->path = $path;
    }

    /**
     * Returns the path of the photo
     *
     * @return string
     */
    public function getPath()
    {
    	return $this->path;
    }
}
