<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Flexix\ModelBundle\Util;

use Flexix\PrototypeControllerBundle\Util\DataAdapter;

/**
 * Description of Adapter
 *
 * @author Mariusz
 */
class PaginatorAdapter extends DataAdapter{

    public function getData() {
    
        return $this->object->getItems();
    }

    public function getTemplateData($templateData) {
        
        $templateVar=$this->driver->getTemplateVar();
        if(!$templateVar)
        {
            $templateVar='paginator';
        }
        $templateData[$templateVar] = $this->object;
        return $templateData;
    }

}
