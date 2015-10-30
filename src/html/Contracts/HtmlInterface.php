<?php

namespace Ycore\Html\Contracts;

interface HtmlInterface
{

    public function add($tag, $attributes = null, $content = null);
    public function wrap($tag, $attributes = null);
    public function _($content);
    public function with($content);
    public function open($tag, $attributes = [], $content = null);
    public function close($tag);
    public function render();
    public function reset();

}
