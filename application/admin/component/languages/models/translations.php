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
 * Translations Model Class
 *
 * @author      Gergo Erdosi <http://nooku.assembla.com/profile/gergoerdosi>
 * @package     Nooku_Server
 * @subpackage  Languages
 */
class LanguagesModelTranslations extends BaseModelDefault
{
    public function __construct(Framework\Config $config)
    {
        parent::__construct($config);
        
        $this->getState()
            ->insert('table', 'cmd')
            ->insert('row', 'int')
            ->insert('iso_code', 'com://admin/languages.filter.iso')
            ->insert('status', 'int')
            ->insert('deleted', 'boolean', false);
    }

    protected function _buildQueryWhere(Framework\DatabaseQuerySelect $query)
    {
        parent::_buildQueryWhere($query);
        $state = $this->getState();
        
        if(!$state->isUnique())
        {
            if($state->search) {
                $query->where('tbl.title LIKE :search')->bind(array('search' => '%'.$state->search.'%'));
            }
            
            if($state->table) {
                $query->where('tbl.table = :table')->bind(array('table' => $state->table));
            }
            
            if($state->row) {
                $query->where('tbl.row = :row')->bind(array('row' => $state->row));
            }
            
            if($state->iso_code) {
                $query->where('tbl.iso_code = :iso_code')->bind(array('iso_code' => $state->iso_code));
            }
            
            if(!is_null($state->status)) {
                $query->where('tbl.status = :status')->bind(array('status' => $state->status));
            }
           	
            if(!is_null($state->deleted)) {
                $query->where('tbl.deleted = :deleted')->bind(array('deleted' => (int) $state->deleted));
            }
        }
    }
    
    protected function _buildQueryOrder(Framework\DatabaseQuerySelect $query)
    {
        if($this->sort == 'table')
        {
            $direction = strtoupper($this->direction);
            
            $query->order('tbl.table', $direction);
      		$query->order('tbl.row', $direction);
      		$query->order('tbl.original', 'DESC');
        }
    }
}