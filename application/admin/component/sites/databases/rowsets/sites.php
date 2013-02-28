<?php
/**
 * @category    Nooku
 * @package     Nooku_Server
 * @subpackage  Sites
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Site Database Rowset Class
 *
 * @author      Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @category    Nooku
 * @package     Nooku_Server
 * @subpackage  Sites
 */
class ComSitesDatabaseRowsetSites extends KDatabaseRowsetAbstract
{       
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'identity_column'   => 'name'
        ));
        
        parent::_initialize($config);
    }
}