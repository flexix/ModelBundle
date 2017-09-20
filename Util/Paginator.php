<?php

namespace Flexix\ModelBundle\Util;

class Paginator {

    protected $paginator;
    protected $limit;

    public function __construct($paginator, $limit=10) {

        $this->paginator = $paginator;
        $this->limit = $limit;
    }

    public function paginate($queryBuilder, $page, $limit = null,$options) {
        
        if (!$limit) {
            $limit = $this->limit;
        }

        $this->paginator->setDefaultPaginatorOptions($options);
        $pagination = $this->paginator->paginate($queryBuilder, $page, $limit);

        return  $pagination;

    }

}
