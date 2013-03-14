<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Timble CVBA and Contributors. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git
 */

namespace Nooku\Component\Categories;

use Nooku\Framework;

/**
 * Listbox Template Helper
 *
 * @author  John Bell <http://nooku.assembla.com/profile/johnbell>
 * @package Nooku\Component\Categories
 */
class TemplateHelperListbox extends \BaseTemplateHelperListbox
{
     public function order($config = array())
     {
         $config = new Framework\Config($config);
         $config->append(array(
             'name'          => 'order',
             'state'         => null,
             'attribs'       => array(),
             'model'         => null,
             'package'       => $this->getIdentifier()->package,
             'selected'      => 0
        ));
        
        //@TODO can be removed when name collisions fixed
        $config->name = 'order'; 

        $app        = $this->getIdentifier()->namespace;
        $identifier = 'com://'.$app.'/'.$config->package.'.model.'.($config->model ? $config->model : Framework\Inflector::pluralize($config->package));

        $list = $this->getService($identifier)->set($config->filter)->getRowset();

        $options = array();
        foreach($list as $item) {
			$options[] =  $this->option(array('text' => $item->ordering, 'value' => $item->ordering - $config->ordering));
		}
		
        $list = $this->optionlist(array(
            'options'  => $options,
            'name'     => $config->name,
            'attribs'  => $config->attribs,
            'selected' => $config->selected
        )); 
        return $list;
     }

    public function categories($config = array())
    {
        $config = new Framework\Config($config);
        $config->append(array(
            'name'      => 'category',
            'deselect'  => true,
            'selected'  => $config->category,
            'prompt'	=> '- Select -',
            'table'     => '',
            'parent'    => '',
            'max_depth' => 9,
        ));

        if($config->deselect) {
            $options[] = $this->option(array('text' => \JText::_($config->prompt), 'value' => 0));
        }

        $list = $this->getService('com://admin/categories.model.categories')
                     ->set('table', $config->table)
                     ->set('parent', $config->parent)
                     ->set('sort', 'title')
                     ->getRowset();

        $iterator = new \RecursiveIteratorIterator($list, \RecursiveIteratorIterator::SELF_FIRST);
        foreach($iterator as $item)
        {
            if($iterator->getDepth() > $config->max_depth) {
                break;
            }

            $title =  substr('---------', 0, $iterator->getDepth()).$item->title;
            $options[] = $this->option(array('text' => $title, 'value' => $item->id));
        }

        $config->options = $options;

        return $this->optionlist($config);
    }
}
