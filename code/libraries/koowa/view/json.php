<?php
/**
 * @version     $Id$
 * @package     Koowa_View
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link         http://www.nooku.org
 */

/**
 * View JSON Class
 *
 * The JSON view implements supports for JSONP through the models callback
 * state. If a callback is present the output will be padded.
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_View
 */
class KViewJson extends KViewAbstract
{
    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param     object     An optional KConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'version' => '1.0'
        ))->append(array(
            'mimetype' => 'application/json; version=' . $config->version,
        ));

        parent::_initialize($config);
    }

    /**
     * Return the views output
     *
     * If the view 'output' variable is empty the output will be generated based on the
     * model data, if it set it will be returned instead.
     *
     * If the model contains a callback state, the callback value will be used to apply
     * padding to the JSON output.
     *
     * @return string     The output of the view
     */
    public function display()
    {
        if (empty($this->output)) {
            $this->output = KInflector::isPlural($this->getName()) ? $this->_getRowset() : $this->_getRow();
        }

        if (!is_string($this->output))
        {
            // Root should be JSON object, not array
            if (is_array($this->output) && 0 === count($this->output)) {
                $this->output = new \ArrayObject();
            }

            // Encode <, >, ', &, and " for RFC4627-compliant JSON, which may also be embedded into HTML.
            $this->output = json_encode($this->output, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
        }

        return parent::display();
    }

    /**
     * Get the list data
     *
     * @return array     The array with data to be encoded to json
     */
    protected function _getRowset()
    {
        //Get the model
        $model = $this->getModel();

        //Get the route
        $route = $this->getRoute();

        //Get the model state
        $state = $model->getState();

        //Get the paginator
        $paginator = new KConfigPaginator(array(
            'offset' => (int)$model->offset,
            'limit' => (int)$model->limit,
            'total' => (int)$model->getTotal(),
        ));

        $vars = array();
        foreach ($state->getStates() as $var)
        {
            if (!$var->unique) {
                $vars[] = $var->name;
            }
        }

        $data = array(
            'version' => '1.0',
            'href' => (string)$route->setQuery($state->toArray(), true),
            'url' => array(
                'type' => 'application/json',
                'template' => (string)$route->getUrl(KHttpUrl::BASE) . '?{&' . implode(',', $vars) . '}',
            ),
            'offset' => (int)$paginator->offset,
            'limit' => (int)$paginator->limit,
            'total' => 0,
            'items' => array(),
            'queries' => array()
        );

        if ($list = $model->getRowset())
        {
            $vars = array();
            foreach ($state->getStates() as $var)
            {
                if ($var->unique)
                {
                    $vars[] = $var->name;
                    $vars = array_merge($vars, $var->required);
                }
            }

            //Singularize the view name
            $name = KInflector::singularize($this->getName());

            $items = array();
            foreach ($list as $item) {
                $id = $item->getIdentityColumn();

                $items[] = array(
                    'href' => (string)$this->getRoute('view=' . $name . '&id=' . $item->{$id}),
                    'url' => array(
                        'type' => 'application/json',
                        'template' => (string)$this->getRoute('view=' . $name) . '?{&' . implode(',', $vars) . '}',
                    ),
                    'data' => $item->toArray()
                );
            }

            $queries = array();
            foreach (array('first', 'prev', 'next', 'last') as $offset) {
                $page = $paginator->pages->{$offset};
                if ($page->active) {
                    $queries[] = array(
                        'rel' => $page->rel,
                        'href' => (string)$this->getRoute('limit=' . $page->limit . '&offset=' . $page->offset)
                    );
                }
            }

            $data = array_merge($data, array(
                'total' => $paginator->total,
                'items' => $items,
                'queries' => $queries
            ));
        }

        return $data;
    }

    /**
     * Get the item data
     *
     * @return array     The array with data to be encoded to json
     */
    protected function _getRow()
    {
        //Get the model
        $model = $this->getModel();

        //Get the route
        $route = $this->getRoute();

        //Get the model state
        $state = $model->getState();

        $vars = array();
        foreach ($state->getStates() as $var)
        {
            if ($var->unique)
            {
                $vars[] = $var->name;
                $vars = array_merge($vars, $var->required);
            }
        }

        $data = array(
            'version' => '1.0',
            'href' => (string)$route->setQuery($state->toArray(true)),
            'url' => array(
                'type' => 'application/json',
                'template' => (string)$route->getUrl(KHttpUrl::BASE) . '?{&' . implode(',', $vars) . '}',
            ),
            'item' => array()
        );

        if ($item = $model->getRow()) {
            $data = array_merge($data, array(
                'item' => $item->toArray()
            ));
        }
        ;

        return $data;
    }

    /**
     * Get a route based on a full or partial query string.
     *
     * This function force the route to be not fully qualified and not escaped
     *
     * @param    string    The query string used to create the route
     * @param     boolean    If TRUE create a fully qualified route. Default FALSE.
     * @param     boolean    If TRUE escapes the route for xml compliance. Default FALSE.
     * @return     string     The route
     */
    public function getRoute($route = '', $fqr = null, $escape = null)
    {
        //If not set force to false
        if ($escape === null) {
            $escape = false;
        }

        return parent::getRoute($route, $fqr, $escape);
    }

}