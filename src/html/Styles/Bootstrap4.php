<?php

namespace Ycore\Html\Styles;

use Ycore\Html\Contracts\StyleInterface;

class Bootstrap4 implements StyleInterface {

    public function fieldset($attributes)
    {
        return array_merge_recursive(['class' => 'form-group'],$attributes);
    }

    public function input($attributes)
    {
        if (isset($attributes['type']) && $attributes['type'] == 'hidden')
        {
            return $attributes;
        }

        return array_merge_recursive(['class' => 'form-control'],$attributes);
    }

    public function button($attributes)
    {
        return array_merge_recursive(['class' => 'btn btn-primary'],$attributes);
    }

}
