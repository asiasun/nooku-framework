<?php
/**
 * @package     Nooku_Server
 * @subpackage  Articles
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

use Nooku\Framework;

/**
 * Html View Class
 *
 * @author      Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package     Nooku_Server
 * @subpackage  Articles
 */

class ApplicationViewPageHtml extends ApplicationViewHtml
{
    protected function _initialize(Framework\Config $config)
    {
        $config->append(array(
            'template_filters' => array('expire', 'module'),
        ));

        parent::_initialize($config);
    }

    public function render()
    {
        // Build the sorted message list
        $messages = $this->getService('application')->getMessageQueue();
        if (is_array($messages) && count($messages))
        {
            foreach ($messages as $message)
            {
                if (isset($message['type']) && isset($message['message'])) {
                    $this->messages[$message['type']][] = $message['message'];
                }
            }
        }
        else  $this->messages = array();


        //Set the component and layout information
        $this->component = $this->getService('application')->getController()->getIdentifier()->package;
        $this->layout    = $this->getService('component')->getController()->getView()->getLayout();

        return parent::render();
    }
}