<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Flexix\ModelBundle\Util;

use Flexix\ModelBundle\Util\PaginatorModel;

/**
 * Description of Typeahead
 *
 * @author Mariusz Piela <mariusz.piela@tmsolution.pl>
 */
class Typeahead extends PaginatorModel {

    protected $queryParameter;
    protected $filteredFields;

    public function __construct($model, $filter, $paginator, $defaultSortOptions = null,$slidingPaginatorSubscriber,$repository,$locale, $queryParameter, $filteredFields = []) {

        $this->queryParameter = $queryParameter;
        $this->filteredFields = array_values($filteredFields);
       // dump($this->filteredFields);
        parent::__construct($model, $filter, $paginator, $defaultSortOptions,$slidingPaginatorSubscriber,$repository,$locale);
    }

    protected function getQuery($request) {
         return $request->query->get($this->queryParameter);
    }

    protected function concat($queryBuilder,$x,$y) {
        return $queryBuilder->expr()->concat($x, $y);
    }

    protected function getDqlFieldsNames($queryBuilder) {

        $expr='';
        $prevExpr='';
       
        for ($i = count($this->filteredFields)-1 ; $i >= 0; $i--) {
           

            if ($i > 0) {

                $expr = $queryBuilder->expr()->concat($queryBuilder->expr()->literal(' '), $this->filteredFields[$i]);
            } else {

                $expr = $this->filteredFields[$i];
            }

            if ($prevExpr) {

                $prevExpr = $this->concat($queryBuilder, $expr, $prevExpr);
            } else {

                $prevExpr = $expr;
            }
        }
    
        return $expr;
    }

    protected function addQueryConditions($query, $queryBuilder) {

        if (count($this->filteredFields) && $query) {
            $dqlFieldsNames = $this->getDqlFieldsNames($queryBuilder);
           
           $queryBuilder->andWhere($queryBuilder->expr()->like($dqlFieldsNames,':query' ) );
           $queryBuilder->setParameter(':query','%'.$query.'%');

        }
        
    }

    protected function prepareQuery($entityClass, $request, $form, $queryBuilder, $driver) {
       
      
        $preparedQuery=parent::prepareQuery($entityClass, $request, $form, $queryBuilder, $driver);
        $query = $this->getQuery($request);
       
        
       
        $this->addQueryConditions($query, $queryBuilder);

        return $preparedQuery;
    }

}
