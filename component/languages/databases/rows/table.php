<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git
 */

namespace Nooku\Component\Languages;

use Nooku\Framework;

/**
 * Table Database Row
 *
 * @author  Gergo Erdosi <http://nooku.assembla.com/profile/gergoerdosi>
 * @package Nooku\Component\Languages
 */
class DatabaseRowTable extends Framework\DatabaseRowTable
{
    public function save()
    {
        $modified = $this->isModified('enabled');
        $result   = parent::save();
        
        if($this->getStatus() == Framework\Database::STATUS_UPDATED && $modified && $this->enabled)
        {
            $database  = $this->getTable()->getAdapter();
            $prefix    = $database->getTablePrefix();
            $languages = $this->getService('application.languages');
            $primary   = $languages->getPrimary();

            foreach($languages as $language)
            {
                if($language->id != $primary->id)
                {
                    $table = strtolower($language->iso_code).'_'.$this->name;
                    
                    // Create language specific table.
                    $query = 'CREATE TABLE '.$database->quoteIdentifier($prefix.$table).
                        ' LIKE '.$database->quoteIdentifier($prefix.$this->name);
                    $database->execute($query);
                    
                    // Copy content of original table into the language specific one.
                    $query = $this->getService('lib:atabase.query.insert')
                        ->table($table)
                        ->values($this->getService('lib:database.query.select')->table($this->name));
                    $database->execute($query);
                    
                    $status   = DatabaseRowTranslation::STATUS_MISSING;
                    $original = 0;
                            
                }
                else
                {
                    $status   = DatabaseRowTranslation::STATUS_COMPLETED;
                    $original = 1;
                }
                
                // Add items to the translations table.
                $select = $this->getService('lib:database.query.select')
                    ->columns(array(
                        'iso_code'  => ':iso_code',
                        'table'     => ':table',
                        'row'       => $this->unique_column,
                        'status'    => ':status',
                        'original'  => ':original'
                    ))
                    ->table($this->name)
                    ->bind(array(
                        'iso_code'  => $language->iso_code,
                        'table'     => $this->name,
                        'status'    => $status,
                        'original'  => $original
                    ));
                
                $query = $this->getService('lib:database.query.insert')
                    ->table('languages_translations')
                    ->columns(array('iso_code', 'table', 'row', 'status', 'original'))
                    ->values($select);
                
                $database->execute($query);
            }
        }
        
        return $result;
    }
}