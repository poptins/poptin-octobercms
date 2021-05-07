<?php namespace Poptin\Poptin;

use Backend;
use Event;
use Db;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name'        => 'poptin.poptin::lang.plugin.name',
            'description' => 'poptin.poptin::lang.plugin.description',
            'author'      => 'poptin.poptin::lang.plugin.author',
            'icon'        => 'plugins/poptin/poptin/poptin-logo.svg'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
         Event::listen('cms.page.end', function(\Cms\Classes\Controller $controller) {
            

            $data =   Db::table('poptin_poptin_info')->select('client_id')->first();
           
            if(isset($data)){
                $client_id = $data->client_id;
                if($client_id){
                    $controller->addJs('https://cdn.popt.in/pixel.js?id='.$client_id);
                }
            }
            
        }); 
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
      
        return [
            'poptin\Poptin\Components\Popup' => 'popup',
        ];
    }
    
    
    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'poptin.poptin.info' => [
                'tab' => 'popup',
                'label' => 'Manage Popup'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        
        return [
            'popup' => [
                'label'       => 'poptin.poptin::lang.plugin.name',
                'url'         => Backend::url('poptin/poptin/info'),
                'iconSvg'        => 'plugins/poptin/poptin/poptin-logo.svg',
                'permissions' => ['poptin.poptin.*'],
                'order'       => 500,
            ],
        ];
    }
}
