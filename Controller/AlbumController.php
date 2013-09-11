<?php 

namespace Ant\PhotoRestBundle\Controller;

use Ant\PhotoRestBundle\Controller\BaseRestController;

use Symfony\Component\HttpFoundation\Request;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use JMS\SecurityExtraBundle\Annotation\SecureParam;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Chatea\ApiBundle\Entity\User;
use Chatea\FotoBundle\Entity\Album;
/**
 * Album controller.
 *
 */
class AlbumController extends BaseRestController
{
	/**
	 * Create a new album entity
	 *  @ApiDoc(
	 *  	description="create an album",
	 *		section="photo",
	 *  	input="Ant\PhotoRestBundle\FormType\AlbumType",
	 *  	output="Ant\PhotoRestBundle\Model\Album",
	 *		statusCodes={
	 *         201="New entity created",
	 *         400="Bad request"
	 *     }
	 *  )
	 *  @SecureParam(name="user", permissions="OWNER,HAS_ROLE_ROLE_ADMIN,HAS_ROLE_APPLICATION")
	 *  @ParamConverter("user", class="ApiBundle:User", options={"error" = "user.entity.unable_find"})
	 */
	public function createAction(User $user, Request $request)
	{
		$albumManager = $this->get('ant.photo_rest.manager.album_manager');
		$album = $albumManager->createAlbum();
		
		$form = $this->get('ant.photo_rest.form_factory.album.default')->createForm();
		$form->setData($album);
		
		$form->bind($request);
		
		if ($form->isValid()) {
			
			$album->setParticipant($user);
			
			$this->get('ant.photo_rest.manager.album_manager')->save($album);
		
			return $this->buildView($album, 201);
		}
		return $this->buildFormErrorsView($form);
	}
	
	/**
	 * Deletes an Album entity.
	 *  @ApiDoc(
	 *  	description="Delete an album",
	 *  	section="album",
	 *  	output="Chatea\ApiBundle\Entity\Album",
	 *		statusCodes={
	 *         200="Returned when successful",
	 *         403="Access denied",
	 *         404="Unable to find Channel entity with code 32"
	 *     }
	 *  )
	 *  @ParamConverter("album", class="FotoBundle:Album", options={"error" = "album.entity.unable_find"})
	 *  @SecureParam(name="album", permissions="OWNER,HAS_ROLE_ROLE_ADMIN")
	 */
	public function deleteAction(Album $album)
	{
		$this->get('ant.photo_rest.manager.album')->delete($channel);
			
		return $this->buildView('Album deleted', 200);
	}
}
