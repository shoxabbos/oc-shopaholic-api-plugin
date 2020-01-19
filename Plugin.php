<?php namespace Shohabbos\Shopaholicapi;

use App;
use System\Classes\PluginBase;
use Illuminate\Foundation\AliasLoader;

class Plugin extends PluginBase
{

	/** @var array Plugin dependencies */
    public $require = ['Lovata.Shopaholic', 'Lovata.Toolbox'];

    public function registerComponents()
    {
    }

    public function registerSettings()
    {
    }

	public function boot()
    {
        	
    }

}
