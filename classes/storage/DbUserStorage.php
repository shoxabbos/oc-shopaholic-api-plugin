<?php namespace Shohabbos\Shopaholicapi\Classes\Storage;

use Model;

/**
 * Class SessionUserStorage
 * @package Lovata\Toolbox\Classes\Storage
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class DbUserStorage extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'db_storage';

    private $user;

    public function __construct() {
        $this->user = UserHelper::instance()->getUser();
    }

    /**
     * Get value from storage
     * @param string $sKey
     * @param mixed  $sDefaultValue
     *
     * @return mixed
     */
    public function get($sKey, $sDefaultValue = null)
    {
        if (empty($sKey)) {
            return $sDefaultValue;
        }

        $obValue = Session::get($sKey, $sDefaultValue);

        return $obValue;
    }

    /**
     * Put value to storage
     * @param string $sKey
     * @param mixed  $obValue
     */
    public function put($sKey, $obValue)
    {
        if (empty($sKey)) {
            return;
        }

        Session::put($sKey, $obValue);
    }

    /**
     * Clear value in storage
     * @param string $sKey
     */
    public function clear($sKey)
    {
        if (empty($sKey)) {
            return;
        }

        Session::forget($sKey);
    }
}
