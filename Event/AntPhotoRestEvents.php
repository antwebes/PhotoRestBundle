<?php

namespace Ant\PhotoRestBundle\Event;

/**
 * Declares all events thrown in the BadgeBundle
 */
final class AntPhotoRestEvents
{
    /**
     * The PHOTO_DELETED event occurs after an user deleted a file of bd
     * The event is an instance of Ant\PhotoRestBundle\Event\PhotoEvent
     *
     * @var string
     */
    const PHOTO_DELETED = 'ant_photo_rest.photo_deleted';
   

}
