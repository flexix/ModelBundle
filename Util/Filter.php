<?php

namespace Flexix\ModelBundle\Util;



/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Filter {

    protected $queryBuilderUpdater;

    public function __construct($queryBuilderUpdater) {
        $this->queryBuilderUpdater = $queryBuilderUpdater;
    }

    public function filter($form, $queryBuilder) {
        
        
        $fitleredQueryBuilder = $this->queryBuilderUpdater->addFilterConditions($form, $queryBuilder);
        return $fitleredQueryBuilder;
    }

}
