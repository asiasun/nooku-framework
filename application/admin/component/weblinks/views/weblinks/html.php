<?php
/**
 * @category    Nooku
 * @package     Nooku_Server
 * @subpackage  Weblinks
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

use Nooku\Framework;

/**
 * Weblinks HTML view class.
 *
 * @author     Tom Janssens <http://nooku.assembla.com/profile/tomjanssens>
 * @category   Nooku
 * @package    Nooku_Server
 * @subpackage Weblinks
 */
class WeblinksViewWeblinksHtml extends Framework\ViewHtml
{
    public function render()
    {        
        $state = $this->getModel()->getState();
        
        // Enable sortable
        $this->sortable = $state->category && $state->sort == 'ordering' && $state->direction == 'asc';
        
        return parent::render();
    }
}