<?php

/**
 * http://www.piprime.fr/1472/efficient-html-code-generation-without-templating-system/
 */

namespace Ycore\Html\Factories;

use Ycore\Html\Contracts\HtmlInterface as HtmlContract;

use Ycore\Html\Exceptions\HtmlException;

class AbstractHtml implements HtmlContract {

    protected static $voidElements = ['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr'];
    protected $skipValueTypes = ['file', 'password', 'checkbox', 'radio'];
    protected $labels = [];
    protected $htmlString = '';
    protected $model;
    protected $style;

    /**
     * Adds a tag to the current instance
     *
     * @param string $tag
     * @param array  $attributes
     * @param string $content
     * @return $this
     */
    public function add($tag, $attributes = [], $content = null)
    {
        if (method_exists($this->style, $tag)) {
            $attributes = call_user_func(array(&$this->style,$tag),$attributes);
        }

        $this->htmlString .= $this->open($tag, $attributes, $content).$this->close($tag);
        return $this;
    }

    /**
     * Wraps existing htmlString content with tag and attributes
     *
     * @param  string $tag
     * @param  array  $attributes
     * @return $this
     */
    public function wrap($tag, $attributes = [])
    {
        $this->htmlString = $this->add($tag, $attributes, $this->htmlString);
        return $this;
    }

    /**
     * Concatenates content to the current instance
     *
     * @param  string $content
     * @return $this
     */
    public function _($content)
    {
        $this->htmlString .= e($content);
        return $this;
    }

    /**
     * Creates a new chainable instance with optional content
     *
     * @param  string $content
     * @return $this
     */
    public function with($content)
    {
        $with = new AbstractHtml;
        $with->_($content);
        return $with;
    }

    /**
     * Returns an opeing tag
     *
     * @param  string $tag
     * @param  array  $attributes
     * @param  string $content
     * @return string
     */
    public function open($tag, $attributes = [], $content = null)
    {
        $tag = strtolower($tag);
        $attributes = self::attributes($attributes);

        // Void elements cannot have content, but self-closing tags are allowed
        return (in_array($tag, self::$voidElements)) ? "<{$tag}{$attributes} />" : "<{$tag}{$attributes}>{$content}";
    }

    /**
     * Returns a closing tag
     *
     * @param  string $tag
     * @return string
     */
    public function close($tag)
    {
        $tag = strtolower($tag);

        // Void elements has self-closing tags
        return (in_array($tag, self::$voidElements)) ? '' : "</{$tag}>";
    }

    /**
    * Set the model instance
    *
    * @param  mixed $model
    *
    * @return void
    */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * Renders the current instance content
     *
     * @return string
     */
    public function render()
    {
        $htmlString = (string) $this->htmlString;
        $this->reset();

        return $htmlString;
    }

    /**
     * Clears the current instance content
     *
     * @return $this
     */
    public function reset()
    {
        $this->htmlString= '';
        return $this;
    }

    /**
     * Build an HTML attribute string from an array.
     *
     * @param array $attributes
     * @return string
     */
    protected static function attributes($attributes)
    {
        $html = [];

        foreach ((array) $attributes as $key => $value) {
            $element = self::attribute($key, $value);

            if (!is_null($element)) {
                $html[] = $element;
            }
        }

        return count($html) > 0 ? ' '.implode(' ', $html) : '';
    }

    /**
     * Build a single attribute element.
     *
     * @param string $key
     * @param string $value
     * @return string
     */
    protected static function attribute($key, $value)
    {
        // For numeric keys we will assume that the key and the value are the same
        // as this will convert HTML attributes such as "required" to a correct
        // form like required="required" instead of using incorrect numerics.
        if (is_numeric($key)) {
            $key = $value;
        }

        if (!is_null($value)) {
            return $key.'="'.e($value).'"';
        }
    }

    protected static function unpackArguments($arguments)
    {
        $attributes = [];
        $content    = null;

        if (count($arguments) > 0)
        {
            // If the first argument is an array, it contains attributes
            if (is_array($arguments[0]))
            {
                $attributes = array_shift($arguments);
            }

            // Whatever is left, (the last or only parameter) contains the content
            $content = array_pop($arguments);
            //$content = end($arguments) ?: null;
        }

        if (!is_array($attributes))
        {
            throw new \InvalidArgumentException("Attributes are invalid. Expected array, received {$attributes}.");
        }

        if (!is_null($content) && !is_string($content) && !$content instanceOf HtmlContract)
        {
            throw new \InvalidArgumentException("Content is invalid. Expected string or HtmlContract, received " . var_export($content, true));
        }

        // If the content is not an object, clean it up
        if (!is_callable($content))
        {
            $content = e($content);
        }

        return array($attributes, $content);
    }

    /**
    * Transform key from array to dot syntax.
    *
    * @param  string $key
    *
    * @return string
    */
    protected static function transformKey($key)
    {
        return str_replace(['.', '[]', '[', ']'], ['_', '', '.', ''], $key);
    }

    /**
    * Get the ID attribute for a field name.
    *
    * @param  string $name
    * @param  array  $attributes
    *
    * @return string
    */
    public function idAttribute($attributes)
    {
        if (array_key_exists('id', $attributes)) {
            return $attributes['id'];
        }

        if (array_key_exists('name', $attributes) && in_array($attributes['name'], $this->labels)) {
            return $attributes['name'];
        }
    }

    /**
    * Get the value that should be assigned to the field.
    *
    * @param  string $name
    * @param  string $value
    *
    * @return mixed
    */
    public function valueAttribute($attributes, $value = null)
    {
        // We will get the appropriate value for the given field. We will look for the
        // value in the session for the value in the old input data then we'll look
        // in the model instance if one is set. Otherwise we will just use empty.

        if (in_array($attributes['type'], $this->skipValueTypes)) {
            return null;
        }

        if (is_null($attributes['name'])) {
            return $value;
        }

        if (!is_null(old(self::transformKey($attributes['name'])))) {
            return old(self::transformKey($attributes['name']));
        }

        if (!is_null($value)) {
            return $value;
        }

        if (isset($this->model)) {
            return data_get($this->model, self::transformKey($attributes['name']));
        }
    }

    /**
     * Build out the label > input id array
     *
     * @param string $id
     */
    public function addLabels($id)
    {
        $this->labels[] = $id;
    }

    /**
     * Reset the label array
     *
     * @return void
     */
    public function resetLabels()
    {
        $this->labels = [];
    }

    /**
     * Dynamically invoke new instance when calling an object
     *
     * @param  string $content
     * @return $this
     */
    public function __invoke($content = '')
    {
        return $this->with($content);
    }

    /**
     * Dynamically adds tags
     *
     * @param  string $method
     * @param  array  $arguments
     * @return $this
     */
    public function __call($method, $arguments)
    {

        $attributes = self::unpackArguments($arguments);

        $this->add($method, $attributes[0], $attributes[1]);

        return $this;
    }

    /**
     * Render the contents when casting to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

}
