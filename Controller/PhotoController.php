<?php 

namespace Ant\PhotoRestBundle\Controller;

use Ant\PhotoRestBundle\Entity\Photo;
use Ant\PhotoRestBundle\EntityManager\PhotoManager;

use Ant\PhotoRestBundle\Model\PhotoInterface;
use Ant\PhotoRestBundle\Model\ParticipantInterface;

use Ant\PhotoRestBundle\Event\AntPhotoRestEvents;
use Ant\PhotoRestBundle\Event\PhotosUserResponseEvent;
use Ant\PhotoRestBundle\Event\PhotoResponseEvent;

use Imagine\Exception\InvalidArgumentException;

use Chatea\UtilBundle\Controller\BaseRestController;

use Symfony\Component\HttpFoundation\Request;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use JMS\SecurityExtraBundle\Annotation\SecureParam;
use JMS\SecurityExtraBundle\Security\Authorization\Expression\Expression;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use FOS\RestBundle\Controller\Annotations\QueryParam;

/**
 * Foto controller.
 *
 */
class PhotoController extends BaseRestController
{
	/**
	 * Create a new photo entity
     *
	 * @ApiDoc(
	 *  	description="create a photo",
	 *		section="photo",
	 *  	input="Ant\PhotoRestBundle\FormType\PhotoType",
	 *  	output="Ant\PhotoRestBundle\Model\Photo",
	 *		statusCodes={
	 *         201="New entity created",
	 *         400="Bad request"
	 *     }
	 *  )
	 *  @SecureParam(name="user", permissions="OWNER,HAS_ROLE_ROLE_ADMIN,HAS_ROLE_APPLICATION")
	 *  @ParamConverter("user", class="ApiBundle:User", options={"error" = "user.entity.unable_find"})
	 */
	public function createAction(ParticipantInterface $user, Request $request)
	{
		$dataRequest = $request->request->all();

		$photoManager = $this->get('ant.photo_rest.entity_manager.photo_manager');
		$photo = $photoManager->createPhoto();

		$form = $this->get('ant.photo_rest.form_factory.photo.default')->createForm();
		$form->setData($photo);

		if ($request->isMethod('POST')) {

			$form->submit($dataRequest);

			if ($form->isValid()) {
				$photo->setParticipant($user);
				if ($request->files->get('image')){
					$image = $request->files->get('image');
				} else {
					$image = $form->getData()->getImage();
				}

				if (!isset($image)){
					return $this->serviceError('photo_rest.file.not_found', '404');
				}

				$url = $this->getPhotoUploader()->upload($image);
				$photo->setPath($url);
				$photoManager->savePhoto($photo);
				return $this->buildResourceView($photo, 200, 'photo_show');
			}
			return $this->buildFormErrorsView($form);
		}
		return $this->render(
				'AntPhotoRestBundle:Photo:add.html.twig',
				array('form'  => $form->createView())
		);
	}
    /**
     * Update a photo entity
     *
     * @ApiDoc(
     *  	description="update a photo",
     *		section="photo",
     *  	input="Ant\PhotoRestBundle\FormType\PhotoTypeUpdate",
     *  	output="Ant\PhotoRestBundle\Model\Photo",
     *		statusCodes={
     *         201="Update entity created",
     *         400="Bad request"
     *     }
     *  )
     *  @ParamConverter("photo", class="FotoBundle:Photo", options={"error" = "photo.entity.unable_find", "id" = "photo_id"})
     */
    public function updateAction(Photo $photo,  Request $request)
    {
    	$photoManager = $this->get('ant.photo_rest.entity_manager.photo_manager');
    	
    	if ($this->isOwnerPhoto($photo, $photoManager)){
    		return $this->createError('This user has no permission for this action', '32', '403');
    	}
    	
        if ('PATCH' === $request->getMethod()){
//             $data  = $request->request->get('ant_photo');
//             $title = array_key_exists('title',$data)? $data['title'] : null;
            
            $data  = $request->request->get('title');
            $title = ($data != null) ? $data : null;
            
            if($title == null){
                return $this->createError('Photo title do not entity', '34', '400');
            }
            try{
                $photo->setTitle($title);

                
                $photoManager->update($photo);
            }catch(BadRequestHttpException $e){
                return $this->serviceError($e->getMessage(), '400');
            }
            return $this->buildResourceView($photo, 200, 'photo_list');
        }
    }
	/**
	 * Show a photo entity
	 * @ApiDoc(
	 *  	description="show a photo",
	 *  	section="photo",
	 *  	output="Ant\PhotoRestBundle\Model\Photo",
	 *		statusCodes={
	 *         200="Returned when successful",
	 *         404="Unable to find Photo entity with code 42"
	 *     }
	 *  )
	 */
	public function showAction(Request $request, $id)
	{
		$photoManager = $this->get('ant.photo_rest.entity_manager.photo_manager');
		$photo = $photoManager->findPhotoById($id);
		
		if (null === $photo) {
			
			return $this->createError('Unable to find Photo entity', '42', '404');
		}
		
		$response = $this->buildResourceView($photo, 200, 'photo_show');
		
		$this->getEventDispatcher()->dispatch(AntPhotoRestEvents::PHOTO_SHOW_COMPLETED, new PhotoResponseEvent($photo, $request, $response));
		
		return $response;
	}

	/**
	 * List all Photo entities of an user
	 * @ApiDoc(
	 *  	description="List all photos of an user",
	 *  	section="photo",
	 *  	output="Ant\PhotoRestBundle\Model\Photo",
	 *		statusCodes={
	 *         200="Returned when successful"
	 *     }
	 *  )
	 * @QueryParam(name="limit", description="Max number of records to be returned")
     * @QueryParam(name="offset", description="Number of records to skip")
	 */
	public function photosUserAction(Request $request, $id)
	{
		$participantManager = $this->get('ant.photo_rest.manager.participant_manager');
		$participant = $participantManager->findParticipantById($id);
		
		if (null === $participant) {
			return $this->createError('Unable to find User entity', '32', '404');
		}
	
		$photoManager = $this->get('ant.photo_rest.entity_manager.photo_manager');
		$entities = $photoManager->findAllMePhotos($participant);
		
		$linkOverrides = array('route' => 'ant_photo_rest_show_user_all', 'parameters' => array('id'), 'rel' => 'self', 'entity' => $participant);
		$response = $this->buildPagedResourcesView($entities, 'Ant\PhotoBundle\Entity\Photo' , 200, 'photo_list', array('id'=>'id'),$linkOverrides);
		
		$this->getEventDispatcher()->dispatch(AntPhotoRestEvents::PHOTO_PHOTOS_USER_COMPLETED, new PhotosUserResponseEvent($entities, $request, $response));
		
		return $response;
	}
	/**
	 * List all Photo entities of an album
	 * @ApiDoc(
	 *  	description="List all Photo entities of an album",
	 *  	section="photo",
	 *  	output="Ant\PhotoRestBundle\Model\Photo",
	 *		statusCodes={
	 *         200="Returned when successful"
	 *     }
	 *  )
	 * @QueryParam(name="limit", description="Max number of records to be returned")
	 * @QueryParam(name="offset", description="Number of records to skip")
	 */
	public function photosAlbumAction($album_id)
	{		
		$album = $this->get('ant.photo_rest.manager.album_manager')->findAlbumById($album_id);
		if (!$album) return $this->createError('Unable to find Album entity', '42', '404');
		
		$entities = $album->getPhotos();
		$parameters = array(
					array('album_id' => 'id')
				);
		
		$linkOverrides = array('route' => 'ant_photo_rest_photos_album', 'parameters' => $parameters, 'rel' => 'self', 'entity' => $album);
		
		return $this->buildPagedResourcesView($entities, 'Ant\PhotoBundle\Entity\Album', 200, 'photo_list', array(), $linkOverrides);
	}
	/**
	 * Delete a photo entity
	 * @ApiDoc(
	 *  	description="delete a photo",
	 *  	section="photo",
	 *  	output="Ant\PhotoRestBundle\Model\Photo",
	 *		statusCodes={
	 *         200="Returned when successful",
	 *         403="Access denied",
	 *         404="Unable to find Photo entity with code 42"
	 *     }
	 *  )
	 */
	public function deleteAction($photo_id)
	{
		$photoManager = $this->get('ant.photo_rest.entity_manager.photo_manager');
		$photo = $photoManager->findPhotoById($photo_id);
		
		if (null === $photo) {
			return $this->createError('Unable to find Photo entity', '42', '404');
		}
		 
		if ($this->isOwnerPhoto($photo, $photoManager)){
			return $this->createError('This user has no permission for this action', '32', '403');
		}
				
		$path = $photo->getPath();
		
		try{
			$photo = $photoManager->deletePhoto($photo);
		}catch (\InvalidArgumentException $e) {
			return $this->buildView($e->getMessage(), 400);
		}
		
		return $this->buildView('Photo deleted', 200);
		
	}
	
	/**
	 * Insert a photo entity into album id
	 * @ApiDoc(
	 *  	description="Insert a photo entity into album id",
	 *		section="photo",
	 *  	output="message confirmation",
	 *		statusCodes={
	 *         200="photo inserted",
	 *         400="Bad request"
	 *     }
	 *  )
	 *  @SecureParam(name="user", permissions="OWNER,HAS_ROLE_ROLE_ADMIN,HAS_ROLE_APPLICATION")
	 *  @ParamConverter("user", class="ApiBundle:User", options={"error" = "user.entity.unable_find", "id" = "user_id"})
	 */
	public function insertToAlbumAction(ParticipantInterface $user, $photo_id, $album_id)
	{
		$photoManager = $this->get('ant.photo_rest.entity_manager.photo_manager');
		
		$photo = $photoManager->findPhotoById($photo_id);		
		if (!$photo) return $this->createError('Unable to find Photo entity', '42', '404');
		
		$securityContext = $this->container->get('security.context');
		if (!($this->get('ant.photo_rest.entity_manager.photo_manager')->isOwner($user, $photo) or $securityContext->isGranted(array(new Expression('hasRole("ROLE_ADMIN") or hasRole("ROLE_APPLICATION")'))))){
			return $this->createError('This user has no permission for this action', '32', '403');
		}
		
		$album = $this->get('ant.photo_rest.manager.album_manager')->findAlbumById($album_id);		
		if (!$album) return $this->createError('Unable to find Album entity', '42', '404');
		
		if ( !($photoManager->isOwner($user, $photo) or $this->get('ant.photo_rest.manager.album_manager')->isOwner($user, $album)
				or $securityContext->isGranted(array(new Expression('hasRole("ROLE_ADMIN") or hasRole("ROLE_APPLICATION")')))
				 )) return $this->createError('This user has no permission for this action', '32', '403');
			
		$photoManager->insertToAlbum($photo, $album);
		
		return $this->buildView('Photo inserted', 200);
	}
	
	/**
	 * Delete a photo entity of an album id
	 * @ApiDoc(
	 *  	description="Delete a photo entity of an album id",
	 *		section="photo",
	 *  	output="message confirmation",
	 *		statusCodes={
	 *         200="photo deleted of an album",
	 *         400="Bad request"
	 *     }
	 *  )
	 *  @SecureParam(name="user", permissions="OWNER,HAS_ROLE_ROLE_ADMIN,HAS_ROLE_APPLICATION")
	 *  @ParamConverter("user", class="ApiBundle:User", options={"error" = "user.entity.unable_find", "id" = "user_id"})
	 */
	public function deleteOfAlbumAction(ParticipantInterface $user, $photo_id)
	{
		$photoManager = $this->get('ant.photo_rest.entity_manager.photo_manager');
	
		$photo = $photoManager->findPhotoById($photo_id);
		if (!$photo) return $this->createError('Unable to find Photo entity', '42', '404');
		
		$securityContext = $this->container->get('security.context');
		
		if ( !($photoManager->isOwner($user, $photo)
				or $securityContext->isGranted(array(new Expression('hasRole("ROLE_ADMIN") or hasRole("ROLE_APPLICATION")')))
		)) return $this->createError('This user has no permission for this action', '32', '403');
		if (!($photo->hasAlbum())){
			return $this->serviceError('photo_rest.album.entity.unable_find', '404');
		}
		$album = $photo->getAlbum();
			
		$photoManager->deleteOfAlbum($photo, $album);
	
		return $this->buildView('Photo deleted of Album', 200);
	}
	
	/**
	 * @return \Ant\PhotoRestBundle\Upload\PhotoUploader
	 */
	
	protected function getPhotoUploader()
	{
		return $this->get('ant.photo_rest.upload.photo_uploader');
	}
	
	/**
	 * verifies that the user is the owner of the photo
	 * @param PhotoInterface $photo
	 * @return \Chatea\UtilBundle\Controller\Response
	 */
	private function isOwnerPhoto(PhotoInterface $photo, PhotoManager $photoManager)
	{
		$securityContext = $this->container->get('security.context');
		 
		$user = $securityContext->getToken()->getUser();
		//if user is not owner or has not Role Admin or application
		return !($photoManager->isOwner($user, $photo) or $securityContext->isGranted(array(new Expression('hasRole("ROLE_ADMIN") or hasRole("ROLE_APPLICATION")'))));
			
	}
	
	protected function getEventDispatcher()
	{
		/** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
		return $this->container->get('event_dispatcher');
	}
}
