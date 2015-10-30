<?php

namespace Ycore\Html;

use Ycore\Html\Factories\AbstractForm;

class FormBuilder extends AbstractForm
{

}

/*

FormInterface
 - define all methods, including abstract (empty) ones
AbstractForm implements FormInterface
- define default method and behaviour
- also define abstract methods to comply with FormInterface
FormBuilder extends AbstractForm implements FormInterface
- define at least abstract methods with behaviour

same for HtmlBuilder

Input can then move to HtmlBuilder
getAttr* can also move to HtmlBuilder (defined in Interface and Abstract)


 */
