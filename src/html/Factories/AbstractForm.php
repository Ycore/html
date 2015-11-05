<?php

namespace Ycore\Html\Factories;

use Illuminate\Routing\UrlGenerator;

use Ycore\Html\Contracts\HtmlInterface;
use Ycore\Html\Contracts\FormInterface;

use Ycore\Html\Exceptions\FormException;

abstract class AbstractForm implements FormInterface
{

    /**
    * The URL generator instance.
    *
    * @var \Illuminate\Routing\UrlGenerator
    */
    protected $url;

    /**
    * The HTML builder instance.
    *
    * @var Ycore\Html\Contracts\HtmlInterface
    */
    protected $html;

    /**
    * The CSRF token used by the form builder.
    *
    * @var string
    */
    protected $csrf;

    /**
    * The reserved form open attributes.
    *
    * @var array
    */
    protected $reserved = ['method', 'url', 'route', 'action', 'files'];

    /**
    * The form methods that should be spoofed, in uppercase.
    *
    * @var array
    */
    protected $spoofedMethods = ['DELETE', 'PATCH', 'PUT'];

    /**
    * Create a new form builder instance.
    *
    * @param  \Illuminate\Routing\UrlGenerator  $url
    * @param Ycore\Html\Contracts\HtmlInterface $html
    * @param  string                            $csrf
    *
    * @return void
    */
    public function __construct(UrlGenerator $url, HtmlInterface $html, $csrf = null)
    {
        $this->html = $html;
        $this->url  = $url;
        $this->csrf = $csrf;
    }

    /**
     * Opens a form tag
     *
     * @param  array  $attributes
     * @return string
     */
    public function open($attributes = [])
    {
        $hiddenMethod = '';
        $hiddenToken  = '';

        $method = strtoupper(array_get($attributes, 'method', 'post'));
        $attributes  = $this->resolveAttributes($attributes, $method);

        if (in_array($method, $this->spoofedMethods)) {
            $hiddenMethod = $this->html->input(['type' => 'hidden', 'name' =>'_method'], $method);
        }

        if ($method != 'GET') {
            $hiddenToken = $this->html->input(['type' => 'hidden', 'name' =>'_token'], $this->token());
        }

        return $this->html->open('FORM',$attributes).$hiddenMethod.$hiddenToken;
    }

    /**
     * Close the form tag
     *
     * @return string
     */
    public function close()
    {
        $this->html->setModel(null);
        $this->html->resetLabels();

        return $this->html->close('FORM');
    }

    /**
    * Create a new model based form builder.
    *
    * @param  mixed $model
    * @param  array $options
    *
    * @return string
    */
    public function model($model, array $attributes = [])
    {
        $this->html->setModel($model);

        return $this->open($attributes);
    }

    /**
     * Renders the content
     *
     * @return string
     */
    public function render()
    {
        return $this->html->render();
    }

    /**
    * Generate a hidden field with the current CSRF token.
    *
    * @return string
    */
    public function token()
    {
        return (!is_null($this->csrf)) ? $this->csrf : csrf_token();
    }
    /**
    * Resolve the form default attributes.
    *
    * @param  string $attributes
    * @return array
    */
    protected function resolveAttributes($attributes, $method)
    {
        $defaults['method'] = $this->resolveMethod($method);
        $defaults['action'] = $this->resolveAction($attributes);
        $defaults['accept-charset'] = 'UTF-8';

        if (isset($attributes['files']) && $attributes['files']) {
            $attributes['enctype'] = 'multipart/form-data';
        }

        $attributes = array_merge(
            $defaults, array_except($attributes, $this->reserved)
        );

        return $attributes;
    }

    /**
    * Resolve the form action method.
    *
    * @param  string $method
    * @return string
    */
    protected function resolveMethod($method)
    {
        return $method != 'GET' ? 'POST' : $method;
    }

    /**
    * Resolve the form action from the attributes.
    *
    * @param  array $attributes
    * @return string
    */
    protected function resolveAction($attributes)
    {

        // We will also check for a "route" or "action" parameter on the array so that
        // developers can easily specify a route or controller action when creating
        // a form providing a convenient interface for creating the form actions.
        if (isset($attributes['url'])) {
            return $this->getAction($attributes['url'], 'to');
        }

        if (isset($attributes['route'])) {
            return $this->getAction($attributes['route'], 'route');
        }

        // If an action is available, we are attempting to open a form to a controller
        // action route. So, we will use the URL generator to get the path to these
        // actions and return them from the method. Otherwise, we'll use current.
        elseif (isset($attributes['action'])) {
            return $this->getAction($attributes['action'], 'action');
        }

        return $this->url->current();
    }

    /**
     * Get the appropriate action url, using the url->method() provided
     *
     * @param  mixed  $attributes
     * @param  string $method
     * @return string
     */
    protected function getAction($attributes, $method)
    {
        if (is_array($attributes)) {
           return $this->url->{$method}($attributes[0], array_slice($attributes, 1));
        }

        return $this->url->{$method}($attributes);
    }

    /**
     * Dynamically call down to HtmlInterface
     *
     * @return string
     */
    public function __call($method, $arguments)
    {
        if ($this->html) {
            return call_user_func_array(array(&$this->html,$method),$arguments);
        }

        throw new \BadMethodCallException("Call to an undefined method {$method}");
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
