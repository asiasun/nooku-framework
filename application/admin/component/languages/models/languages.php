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
 * Languages Model Class
 *
 * @author      Gergo Erdosi <http://nooku.assembla.com/profile/gergoerdosi>
 * @package     Nooku_Server
 * @subpackage  Languages
 */
class LanguagesModelLanguages extends BaseModelDefault
{
    public function __construct(Framework\Config $config)
    {
        parent::__construct($config);

        $this->getState()
            ->insert('primary', 'boolean')
            ->insert('enabled', 'boolean')
            ->insert('application', 'word', 'admin', true, array('iso_code'))
            ->insert('iso_code', 'com://admin/languages.filter.iso', null, true, array('application'));
    }

    protected function _buildQueryWhere(Framework\DatabaseQuerySelect $query)
    {
        parent::_buildQueryWhere($query);
        $state = $this->getState();

        if(!$state->isUnique())
        {
            if(!is_null($state->primary)) {
                $query->where('tbl.primary = :primary')->bind(array('primary' => (int) $state->primary));
            }

            if(!is_null($state->enabled)) {
                $query->where('tbl.enabled = :enabled')->bind(array('enabled' => (int) $state->enabled));
            }

            if($state->application) {
                $query->where('tbl.application = :application')->bind(array('application' => $state->application));
            }
        }
    }
}