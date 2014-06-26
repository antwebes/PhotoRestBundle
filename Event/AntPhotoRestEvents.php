<?php

namespace Ant\PhotoRestBundle\Event;

/**
 * Declares all events thrown in the BadgeBundle
 */
final class AntPhotoRestEvents
{
    /**
     * The PHOTO_PRE_DELETE event occurs before an user deleted a photo of bd
     * The event is an instance of Ant\PhotoRestBundle\Event\PhotoEvent
     *
     * @var string
     */
    const PHOTO_PRE_DELETE = 'ant_photo_rest.photo_pre_delete';

    /**
     * The PHOTO_DELETED event occurs after an user deleted a photo of bd
     * The event is an instance of Ant\PhotoRestBundle\Event\PhotoEvent
     *
     * @var string
     */
    const PHOTO_DELETED = 'ant_photo_rest.photo_deleted';
    
    /**
     * The PHOTO_SHOW_COMPLETED event occurs after to recuperato a photo and before that send response
     * The event listener method receives a Ant\PhotoRestBundle\Event\PhotoResponseEvent
     *
     * @var string
     */
    const PHOTO_SHOW_COMPLETED = 'ant_photo_rest.photo.show.completed';
    
    /**
     * The PHOTO_PHOTOS_USER_COMPLETED event occurs after to recuperate all Photo entities of an user before that send response
     * The event listener method receives a Ant\PhotoRestBundle\Event\PhotoResponseEvent
     *
     * @var string
     */
    const PHOTO_PHOTOS_USER_COMPLETED = 'ant_photo_rest.photo.photos_user.completed';
   

}
