<?php

namespace QPress\Breadcrumb;

class Breadcrumb
{

    public $items = array();
    public $attrs = array();
    public $current;

    /**
     * Constructor, globally sets $items array
     *
     * @param   array   Array of list items (instead of using add() method)
     * @return  void
     */
    public function __construct(array $items = NULL)
    {
        $this->items = $items;
    }

    /**
     * Add's a new list item to the menu
     *
     * @chainable
     * @param   string   Title of link
     * @param   string   URL (address) of link
     * @return  Breadcrumb
     */
    public function add($title, $url = null)
    {
        $this->items[] = array
            (
            'title' => $title,
            'url' => $url,
        );

        return $this;
    }

    /**
     * renders the breadcrumbs
     *
     * @param   array   array with tag attribute settings
     * @access        public
     * @return        void
     */
    public function render(array $attributes = array())
    {
        if (empty($this->items))
        {
            return;
        }
        
        if (!isset($attributes['class'])) {
            $attributes['class'] = 'breadcrumb';
        }

        $template = get_contents(__DIR__ . '/Resources/views/breadcrumb.php', array(
            'links' => $this->items,
            'attributes' => array_to_attr($attributes)
        ));

        echo $template;
    }

    /**
     * Compiles an array of HTML attributes into an attribute string.
     *
     * @param   string|array  array of attributes
     * @return  string
     */
    protected static function attributes($attrs)
    {
        if (empty($attrs))
            return '';

        if (is_string($attrs))
            return ' ' . $attrs;

        $compiled = '';
        foreach ($attrs as $key => $val)
        {
            $compiled .= ' ' . $key . '="' . htmlspecialchars($val) . '"';
        }

        return $compiled;
    }

}
