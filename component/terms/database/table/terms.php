<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git
 */

namespace Nooku\Component\Terms;

use Nooku\Framework;

/**
 * Description
 *   
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Component\Terms
 */
class DatabaseTableTerms extends Framework\DatabaseTableDefault
{
    protected function _initialize(Framework\Config $config)
    {
        $config->append(array(
            'name'       => 'terms',
            'behaviors'  => array(
                'creatable', 'modifiable', 'lockable', 'sluggable'
            )
        ));

        parent::_initialize($config);
    }
}