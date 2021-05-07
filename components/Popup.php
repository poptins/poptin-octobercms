<?php

namespace Poptin\Poptin\Components;

use Cms\Classes\ComponentBase;
use Db;

class Popup extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'Poptin Popup',
            'description' => 'A simple custom form'
        ];
    }
    
    public function prepareVars()
    {
        $this->page['client_id'] = $this->getPoptin();
    }

    /**
     * Executed when this component is bound to a page or layout.
     */
    public function onRun()
    {
       

        
        $this->prepareVars();
    }
    
    
    
    public function getPoptin(){
       $data =   Db::table('poptin_poptin_info')->select('POPTIN_CLIENT_ID')->first();
       return $data->POPTIN_CLIENT_ID;
    }

}