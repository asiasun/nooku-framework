<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git
 */

namespace Nooku\Component\Versions;

use Nooku\Library;

/**
 * Revision Database Row
 *
 * @author  Torkil Johnsen <http://nooku.assembla.com/profile/torkiljohnsen>
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Component\Versions
 */
class DatabaseRowRevision extends Library\DatabaseRowTable
{
	public function __get($column)
    {
    	if($column == 'data' && is_string($this->_data['data'])) {
			$this->_data['data'] = json_decode($this->_data['data'], true);
		}

    	return parent::__get($column);
    }

    public function setStatus($status)
    {
        if($status == 'trashed') {
            parent::setStatus(Library\Database::STATUS_DELETED);
        }

        $this->_status = $status;
        return $this;
    }
}