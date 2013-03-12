<?php
/**
 * @package     Nooku_Server
 * @subpackage  Languages
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

use Nooku\Framework;

/**
 * Languages Database Table Class
 *
 * @author      Gergo Erdosi <http://nooku.assembla.com/profile/gergoerdosi>
 * @package     Nooku_Server
 * @subpackage  Languages
 */

class LanguagesDatabaseTableLanguages extends Framework\DatabaseTableAbstract
{
    protected function _initialize(Framework\Config $config)
    {
        $config->append(array(
            'name'      => 'languages',
            'behaviors' => array(
                'sluggable' => array('columns' => array('name'))
            ),
            'filters'   => array(
                'iso_code'  => array('com://admin/languages.filter.iso'),
		    )
        ));

        parent::_initialize($config);
    }
}