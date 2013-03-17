<?php
/**
 * @package     Nooku_Server
 * @subpackage  Articles
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

use Nooku\Framework;

/**
 * Articles Controller Default Class
 *
 * @author      Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package     Nooku_Server
 * @subpackage  Articles
 */
class ArticlesControllerDefault extends BaseControllerModel
{ 
    protected function _initialize(Framework\Config $config)
    {
        $config->append(array(
        	'behaviors' => array('com:activities.controller.behavior.loggable'),
        ));
    
        parent::_initialize($config);
    }
}