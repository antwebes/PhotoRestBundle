<?php

namespace Ant\PhotoRestBundle\Event;

use Ant\PhotoRestBundle\Model\ParticipantInterface;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\EventDispatcher\Event;

class UserEvent extends Event
{
    
    private $user;

    public function __construct(ParticipantInterface $user)
    {
    	$this->user = $user;
    }

    /**
     * Returns the path of the photo
     *
     * @return string
     */
    public function getUser()
    {
    	return $this->user;
    }
}
