<?php
/**
 * @author Rytis Grincevicius <rytis@kiberzauras.com>
 * @link http://www.github.com/kiberzauras/laravel.multilanguage
 * @version 2.0.4
 * @license MIT
 */
namespace Kiberzauras\MultiLanguage;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Illuminate\Http\Request as LaravelRequest;

class Request extends LaravelRequest
{
    /** There we will check if first segment of request uri is one our supported
     * languages, then we will set it to locale and remove from request, this way
     * there will be no need to configure our routes.php file for any of languages.
     * @return LaravelRequest
     *
     * @todo If request doesn't have language, it would be great to check browser language or location.
     */
    public static function capture()
    {
        $config_url = config_path() . DIRECTORY_SEPARATOR . 'multilanguage.json';
        if (file_exists($config_url)) {
            $params = json_decode(file_get_contents($config_url));

            if (!empty($params->enabled)) {

                if (array_key_exists('HTTP_X_ORIGINAL_URL', $_SERVER))
                    self::parseServerVars('HTTP_X_ORIGINAL_URL', $params);

                elseif (array_key_exists('HTTP_X_REWRITE_URL', $_SERVER))
                    self::parseServerVars('HTTP_X_REWRITE_URL', $params);

                elseif (array_key_exists('UNENCODED_URL', $_SERVER) && $_SERVER['IIS_WasUrlRewritten'] == 1)
                    self::parseServerVars('UNENCODED_URL', $params);

                elseif (array_key_exists('REQUEST_URI', $_SERVER))
                    self::parseServerVars('REQUEST_URI', $params);

                elseif (array_key_exists('ORIG_PATH_INFO', $_SERVER))
                    self::parseServerVars('ORIG_PATH_INFO', $params);
            }
            defined('Language') || define('Language', !empty($params->default) ? $params->default : 'en');
        }

        static::enableHttpMethodParameterOverride();
        return static::createFromBase(SymfonyRequest::createFromGlobals());
    }

    /**
     * @param $var
     * @param $params
     */
    protected static function parseServerVars($var, $params)
    {
        $uri = trim($_SERVER[$var], '/');
        $lang = strstr($uri, '/', true);
        if (in_array($lang, $params->enabled)) {
            // for accessing /en/page/page
            $_SERVER[$var] = strstr($uri, '/');
            define('Language', $lang);
        } elseif (in_array($uri, $params->enabled)) {
            // for accessing /, /en, and /en/ pages
            $_SERVER[$var] = '/';
            define('Language', $uri);
        }
    }
}
