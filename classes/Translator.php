<?php namespace RainLab\Translate\Classes;

use App;
use Schema;
use Session;
use DbDongle;
use RainLab\Translate\Models\Locale;

/**
 * Translate class
 *
 * @package rainlab\translate
 * @author Alexey Bobkov, Samuel Georges
 */
class Translator
{

    use \October\Rain\Support\Traits\Singleton;

    const SESSION_LOCALE = 'rainlab.translate.locale';

    const SESSION_CONFIGURED = 'rainlab.translate.configured';

    /**
     * @var string The locale to use on the front end.
     */
    protected $activeLocale;

    /**
     * @var string The default locale if no active is set.
     */
    protected $defaultLocale;
    
    /**
     * @var boolean Determine if translate plugin is configured and ready to be used.
     */
    protected $isConfigured;

    public function init()
    {
        $this->defaultLocale = $this->isConfigured() ? array_get(Locale::getDefault(), 'code', 'en') : 'en';
        $this->activeLocale = $this->defaultLocale;
    }

    public function setLocale($locale)
    {
        App::setLocale($locale);
        $this->activeLocale = $locale;
    }

    public function getLocale($fromSession = false)
    {
        return $this->activeLocale;
    }

    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    public function isConfigured()
    {
        if ($this->isConfigured !== null)
            return $this->isConfigured;

        if (Session::has(self::SESSION_CONFIGURED)) {
            $result = true;
        }
        elseif (DbDongle::hasDatabase() && Schema::hasTable('rainlab_translate_locales')) {
            Session::put(self::SESSION_CONFIGURED, true);
            $result = true;
        }
        else {
            $result = false;
        }

        return $this->isConfigured = $result;
    }

    //
    // Session handling
    //

    public function getSessionLocale()
    {
        if (!Session::has(self::SESSION_LOCALE))
            return null;

        return Session::get(self::SESSION_LOCALE);
    }

    public function setSessionLocale($locale)
    {
        Session::put(self::SESSION_LOCALE, $locale);
    }

    public function loadLocaleFromSession()
    {
        if ($sessionLocale = $this->getSessionLocale())
            $this->setLocale($sessionLocale);
    }

}