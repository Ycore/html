<?php

namespace Ycore\Html\Contracts;

interface FormInterface
{
    function open($attributes = []);
    function close();
    function model($model, array $attributes = []);
    function token();
}
