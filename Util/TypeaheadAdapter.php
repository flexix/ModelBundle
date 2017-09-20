<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Flexix\ModelBundle\Util;

use Flexix\PrototypeControllerBundle\Util\DataAdapter;
use Flexix\ModelBundle\Util\PaginatorAdapter;

/**
 * Description of Adapter
 *
 * @author Mariusz
 */
class TypeaheadAdapter extends PaginatorAdapter{

    public function getData() {
    
        $paginationData=$this->object->getPaginationData();
        
        if(($paginationData["last"]-$paginationData["current"])>0)
        {
            $next=$paginationData["current"]+1;
        }    
        
        $results=[];
        
        foreach($this->object->getItems() as $item)
        {
            $record=[];
            $record['text']=(string)$item;
            $record['id']=$item->getId();
            $results[]=$record;
        }
        
        $data=[
            
            "more"=>isset($next)?$next:false,
            "results"=>$results,
            "pageParameterName"=>$this->object->getPaginatorOptions()['pageParameterName']
        ];
        
        return $data;
    }


}
