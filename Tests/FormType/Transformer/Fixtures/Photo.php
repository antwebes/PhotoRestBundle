<?php
/**
 * User: José Ramón Fernandez Leis
 * Email: jdeveloper.inxenio@gmail.com
 * Date: 9/09/15
 * Time: 15:52
 */

namespace Ant\PhotoRestBundle\Tests\FormType\Transformer\Fixtures;

use Ant\PhotoRestBundle\Entity\Photo as BasePhoto;


class Photo extends BasePhoto
{
    protected $id;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


}