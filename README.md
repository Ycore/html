# Ycore\html
A Fluent Form and HTML Markup Builder for Laravel 5.1

## Installation

To get the latest version of Laravel HTMLMin, simply add the following line to the require block of your `composer.json` file:

```
"ycore/html": "~0.1.4"
```

You'll then need to run `composer install` or `composer update` to download it and have the autoloader updated.

Once Ycore\html is installed, you need to register the service provider. Open up `config/app.php` and add the following to the `providers` key.

* `'Ycore\html\Providers\HtmlServiceProvider'`

## Usage

The simplest way to use the Form and Html Builder is to inject the service directly into the blade template

```
@inject('form', 'Ycore\Html\FormInterface')

```

This retrieves the service from the Laravel service container. The first argument passed to @inject is the name of the variable the service will be placed into.

You are now able to Fluently build up any HTML markup structure you wish.

Example of the standard Login form using Twitter Bootstrap 4 markup

``` php
    <div class="card">
        <div class="card-header">Login
        </div>
        <div class="card-block">
            {!! $form->open(['method' => 'POST']) !!}
                {!!  $form->FIELDSET($form
                        ->LABEL(['for' => 'email'],'Email')
                        ->INPUT(['type' => 'email', 'name' => 'email', 'placeholder' => 'Enter your login e-mail adress'])
                    )
                !!}
                {!!  $form->FIELDSET($form
                        ->LABEL(['for' => 'password'],'Password')
                        ->INPUT(['type' => 'password', 'name' => 'password', 'placeholder' => 'Enter your password'])
                    )
                !!}
                {!!  $form->DIV(['class' => 'checkbox'],$form
                        ->LABEL($form
                            ->INPUT(['type' => 'checkbox', 'name' => 'remember'])
                            ->DIV('Remember Me')
                        )
                    )
                !!}
                {!!  $form->DIV($form
                        ->BUTTON(['type' => 'submit'], 'Login')
                    )
                !!}
            {!! $form->close() !!}
        </div>
    </div>
```

## Testing

You'd need a laravel application to test the package using the test suite

Install a laravel/laravel application as per the instructions. Then, in the laravel home directory;

``` bash

$ cd tests
$ ln -s ../[relative path to package]/../ycore/html/tests/html html

```

This creates a symbolic relative link to the package tests directory inside the application test directory. You are now able to run phpunit tests agains this directory.

``` bash

$ phpunit tests/html

```

## License

Ycore/HTML is licensed under [The MIT License (MIT)](LICENSE).
