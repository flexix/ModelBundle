<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Flexix\ModelBundle\Util;

/**
 * Description of Typeahead
 *
 * @author Mariusz
 */
class Typeahead {

    protected $model;
    protected $filter;
    protected $acceptedFields;

    public function __construct($model, $filter, $limit, $acceptedFields = []) {
        $this->model = $model;
        $this->filter = $filter;
        $this->acceptedFields = $acceptedFields;
    }

    public function find($entityClass, $request, $form) {

        $field = $request->get('field');

        if (array_key_exists($field, $this->acceptedFields)) {

            $queryBuidler = $this->model->getQueryBuilder($entityClass);
            $queryBuidler->select($this->acceptedFields[$field]);
            if ($form) {
                $queryBuilder = $this->filter->filter($form, $queryBuidler);
            }
 
            //var_dump($queryBuidler->getQuery()->getResult());
            return $queryBuidler->getQuery()->getResult();
        } else {
            throw new \Exception(sprintf('The "%s" is not accepted', $field));
        }
    }

}
