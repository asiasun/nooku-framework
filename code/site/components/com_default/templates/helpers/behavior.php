<?php
/**
 * @version     $Id$
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */


/**
 * Date Helper
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultTemplateHelperBehavior extends KTemplateHelperBehavior
{
    /**
     * Keep session alive
     *
     * This will send an ascynchronous request to the server via AJAX on an interval
     *
     * @return string   The html output
     */
    public function keepalive($config = array())
    {
        $session = $this->getService('application.session');
        if($session->isActive())
        {
            //Get the config session lifetime
            $lifetime = $session->getLifetime() * 1000;

            //Refresh time is 1 minute less than the liftime
            $refresh =  ($lifetime <= 60000) ? 30000 : $lifetime - 60000;

            $config = new KConfig($config);
            $config->append(array(
                'refresh' => $refresh
            ));

            return parent::keepalive($config);
        }
    }
}