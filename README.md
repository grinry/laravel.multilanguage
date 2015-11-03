## Multilanguage for Laravel 5.1

[![Total Downloads](https://poser.pugx.org/kiberzauras/laravel.multilanguage/d/total.svg)](https://packagist.org/packages/kiberzauras/laravel.multilanguage)
[![Latest Stable Version](https://poser.pugx.org/kiberzauras/laravel.multilanguage/v/stable.svg)](https://packagist.org/packages/kiberzauras/laravel.multilanguage)
[![Latest Unstable Version](https://poser.pugx.org/kiberzauras/laravel.multilanguage/v/unstable.svg)](https://packagist.org/packages/kiberzauras/laravel.multilanguage)
[![License](https://poser.pugx.org/kiberzauras/laravel.multilanguage/license.svg)](https://packagist.org/packages/kiberzauras/laravel.multilanguage)

This package will help you to easy create multilanguage routes on top of your single language website. With this package
 you will be able to access your website with these routes example.com, example.com/en, example.com/en/page and etc. and
 there is no need to change your routes.php file!
!Note: will not work if you use microsoft IIS as your server. I will try to solve this issue soon.

### Installation

At first you need to install our package:

    composer require "kiberzauras/laravel.multilanguage"

Then you need to create new file under /config/multilanguage.json with this content:

    {"default":"en","enabled":["en","ru","lt"]}
    

In /public/index.php file change these lines:

    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
With these lines:

    $response = $kernel->handle(
        $request = Kiberzauras\MultiLanguage\Request::capture()
    );
Add new provider (/config/app.php providers[]):

    Kiberzauras\MultiLanguage\MultiLanguageServiceProvider::class
Thats it, your application now can be accessed with url like these:

    example.com
    example.com/
    example.com/en
    example.com/en/
    example.com/admin/page/etc
    example.com/en/admin/page/etc
Now, try creating hyperlinks:

    <?= URL::to('main/index'); ?> //or
    <?= url('main/index'); ?> // will create route to /en/main/index (it will use default language as prefix)
    <?= URL::to('main/index', ['language'=>'ru']) // will create create route/change default language to /ru/main/index
    <?= route('profile'); ?>
    <?= route('profile', ['language'=>'ru']); ?>
You can access current language like before:

    App::getLocale();

### License

The Laravel Multilanguage is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
