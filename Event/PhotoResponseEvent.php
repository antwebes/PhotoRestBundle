<?php

namespace Ant\PhotoRestBundle\Event;

use Ant\PhotoRestBundle\Model\PhotoInterface;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class PhotoResponseResponseEvent extends Event
{
	private $request;
	private $user;
    private $response;

    public function __construct(PhotoInterface $photo, Request $request, Response $response)
    {
    	$this->photo = $photo;
    	$this->request = $request;
        $this->response = $response;
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
    
    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}