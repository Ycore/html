<?php

namespace Ycore\Html;

use Illuminate\Container\Container;

use Ycore\Html\Contracts\HtmlInterface;
use Ycore\Html\Contracts\StyleInterface;
use Ycore\Html\Factories\AbstractHtml;
use Ycore\Html\Exceptions\HtmlException;

class HtmlBuilder extends AbstractHtml implements HtmlInterface
{

    public function __construct(StyleInterface $style)
    {
        $this->style = $style;
    }

    /**
    * Create an input field.
    *
    * @param  string $value
    * @param  array  $options
    *
    * @return string
    */
    public function input($attributes = [], $value = null)
    {

        if (!array_keys_exist(['type', 'name'], $attributes)) {
            throw new HtmlException('The input tag requires type and name attributes');
        }

        $id = $this->idAttribute($attributes);
        $value = $this->valueAttribute($attributes, $value);
        $attributes = array_merge($attributes, compact('type', 'value', 'id'));

        return $this->add('INPUT',$attributes);
    }

}
