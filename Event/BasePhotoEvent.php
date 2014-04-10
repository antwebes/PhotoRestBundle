<?php

namespace Ant\PhotoRestBundle\Event;

use Ant\PhotoRestBundle\Model\PhotoInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\EventDispatcher\Event;

class BasePhotoEvent extends Event
{
    
	private $request;
    private $photo;

    public function __construct(PhotoInterface $photo, Request $request)
    {
        $this->photo = $photo;
        $this->request = $request;
    }

    /**
     * @return PhotoInterface
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
