<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

namespace Nooku\Library;

/**
 * Object Instantiable Interface
 *
 * The interface signals the ObjectManager to delegate object instantiation.
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Object
 * @see         ObjectManager::get()
 */
interface ObjectInstantiable
{
    /**
     * Instantiate the object
     *
     * @param 	ObjectConfig            $config	  A ObjectConfig object with configuration options
     * @param 	ObjectManagerInterface	$manager  A ObjectManagerInterface object
     * @return  object
     */
    public static function getInstance(ObjectConfig $config, ObjectManagerInterface $manager);
}