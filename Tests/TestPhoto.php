<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ant\PhotoRestBundle\Tests;

use Ant\PhotoRestBundle\Model\Photo;

class TestPhoto extends Photo
{
    public function setId($id)
    {
        $this->id = $id;
    }
}
