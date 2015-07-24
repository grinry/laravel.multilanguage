<?php
/**
 * @author Rytis Grincevicius <rytis@kiberzauras.com>
 * @license MIT
 */
namespace Kiberzauras\MultiLanguage;

use Illuminate\Support\Facades\Facade;
/**
 * @see \Kiberzauras\MultiLanguage\UrlGenerator
 */
class URL extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'url';
    }
}
