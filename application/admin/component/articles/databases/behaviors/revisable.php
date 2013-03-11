<?php
/**
 * @package     Nooku_Server
 * @subpackage  Articles
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

use Nooku\Framework;
use Nooku\Component\Versions;

/**
 * Revisable Database Behavior Class
 *
 * @author      Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package     Nooku_Server
 * @subpackage  Articles
 */
class ComArticlesDatabaseBehaviorRevisable extends Versions\DatabaseBehaviorRevisable
{
    protected function _selectRevisions($table, $status, Framework\DatabaseQueryInterface $query)
    {
        $result = parent::_selectRevisions($table, $status, $query);

        //Filter the rowset based on the category id
        if($query->params->has('categories_category_id'))
        {
            $needle = array();
            $needle['categories_category_id'] = $query->params->get('categories_category_id');

            $result = $result->find($needle);
        }

        return $result;
    }
}