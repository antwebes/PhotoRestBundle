<?php

namespace Ant\PhotoRestBundle\ModelManager;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Ant\PhotoRestBundle\Model\ParticipantInterface;
use Ant\PhotoRestBundle\Model\PhotoInterface;
use Ant\PhotoRestBundle\Model\AlbumInterface;
use Ant\PhotoRestBundle\Event\AntPhotoRestEvents;
use Ant\PhotoRestBundle\Event\PhotoEvent;

use Gaufrette\Filesystem;

/**
 * With this class you can create a entity Photo, without class final and independent of ORM
 * @author Chrysweel
 *
 */

abstract class PhotoManager implements PhotoManagerInterface
{
	protected $validOrderFields = array('id','title', 'enabled','participant','score', 'numberVotes');

	/**
	 * @var Filesystem
	 */
	protected $fileSystem;
	
	protected $eventDispatcher;

	public function __construct(Filesystem $fileSystem, EventDispatcherInterface $eventDispatcher)
	{
		$this->fileSystem = $fileSystem;
		$this->eventDispatcher = $eventDispatcher;
	}

	public function savePhoto(PhotoInterface $photo)
	{
		$this->doSavePhoto($photo);
	}
	
	public function deletePhoto(PhotoInterface $photo)
	{
		$this->eventDispatcher->dispatch(AntPhotoRestEvents::PHOTO_PRE_DELETE, new PhotoEvent($photo));
        $this->deleteFile($photo->getPath());
        $this->doDeletePhoto($photo);
        $this->eventDispatcher->dispatch(AntPhotoRestEvents::PHOTO_DELETED, new PhotoEvent($photo->getPath()));

	}

	public function deleteFile($path)
	{
        if($this->fileSystem->has($path)){
            $this->fileSystem->delete($path);
        }
	}

    /**
     * @return PhotoInterface
     */
	public function createPhoto()
	{
		$class = $this->getClass();
		$photo = new $class;
		
		return $photo;
	}
	/**
	 * @param string $id
	 * @return PhotoInterface
	 */
	public function findPhotoById($id)
	{
		return $this->findOnePhotoBy(array('id' => $id));
	}
	/**
	 * get all photos of an user
     * @param ParticipantInterface $participant
     * @return PhotoInterface|null
     */
	public function findAllMePhotos(ParticipantInterface $participant, $orderString = null)
	{
		if($orderString === null){
			$order = array();
		}else{
			$order = $this->parseOrders($orderString);
		}

		return $this->findPhotoBy(array('participant' => $participant), $order);
	}
    /**
     * Calculate the new score of a photo with the new vote
     * @param PhotoInterface $photo
     * @param $newVote
     * @param $operator
     */
	public function updateScore(PhotoInterface $photo, $newVote, $operator)
	{
		$scoreOld = $photo->getScore();
		
		$votesOld = $photo->getNumberVotes();

		if ($operator == '+'){
			$newScore = ($votesOld * $scoreOld + $newVote) / ($votesOld + 1);
			$photo->setScore($newScore);
			$photo->setNumberVotes($votesOld + 1);
		}
		else if ($operator == '-'){
			
			if ( ($votesOld - 1) != 0 ){
				$newScore = ($votesOld * $scoreOld - $newVote) / ($votesOld - 1) ;
			} else $newScore = null;
			$photo->setScore($newScore);
			$photo->setNumberVotes($votesOld - 1);
		}
		
		$this->doSavePhoto($photo);
	}

    /**
     * @param ParticipantInterface $user
     * @param PhotoInterface $photo
     * @return bool
     */
	public function isOwner(ParticipantInterface $user, PhotoInterface $photo)
	{
		return ($user->getId() == $photo->getParticipant()->getId());
	}

    /**
     * @param PhotoInterface $photo
     * @param AlbumInterface $album
     */
	public function insertToAlbum(PhotoInterface $photo, AlbumInterface $album)
	{
		$album->addPhoto($photo);
		
		$this->doSavePhoto($photo);		
	}

    /**
     * @param PhotoInterface $photo
     * @param AlbumInterface $album
     */
	public function deleteOfAlbum(PhotoInterface $photo, AlbumInterface $album)
	{
		$album->getPhotos()->removeElement($photo);
		$photo->setAlbum(null);
		
		$this->doSavePhoto($photo);
	}

	/**
	 * Convierte un string de ordenación con patron campo=ORDEN[,campo=ORDEN...] en un array asociativo.
	 *
	 * Ejemplo:
	 *
	 * paresOrders("campo1=asc,campo2=desc") = array('campo1' => 'asc', 'campo2' => 'desc')
	 *
	 * @param $orderString
	 *
	 * @return array
	 */
	protected function parseOrders($orderString)
	{
		$orders = $this->extractOdersParts($orderString);
		return $this->fileterValidOrders($orders);
	}

	private function extractOdersParts($orderString)
	{
		$orders = explode(',', $orderString);
		return array_map(function($order){
			return explode('=', $order);
		}, $orders);
	}

	private function fileterValidOrders($orders)
	{
		//TODO XABIER, mover esto a un fichero de configuración
		$validOrderFields = $this->validOrderFields;
		return array_filter($orders, function($order) use ($validOrderFields){
			return count($order) == 2 &&
			in_array($order[0], $validOrderFields) &&
			in_array(strtolower($order[1]), array('asc', 'desc'));
		});
	}
}