<?php
 /*
 * This file (PhotoTypeUpdate.php) is part of the apiChatea project.
 *
 * 2013 (c) Ant-Web S.L.  
 * Created by Javier Fernández Rodríguez <jjbier@gmail.com>
 * Date: 17/12/13 - 18:05
 *  
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 */

namespace Ant\PhotoRestBundle\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class PhotoTypeUpdate
 * @package Ant\PhotoRestBundle\FormType
 */
class PhotoTypeUpdate extends AbstractType
{
    private $photoClass;

    public function __construct($photoClass)
    {
        $this->photoClass = $photoClass;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'data_class' => $this->photoClass,
            'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return "ant_photo";
    }
}