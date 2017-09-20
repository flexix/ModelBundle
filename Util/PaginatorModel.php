<?php

namespace Flexix\ModelBundle\Util;

class PaginatorModel {

    protected $model;
    protected $filter;
    protected $paginator;
    protected $defaultSortOptions;
    protected $repository;
    protected $locale;

    const REPLACE_MASK = '/[A-Z]([A-Z](?![a-z]))*/';

    public function __construct($model, $filter, $paginator, $defaultSortOptions = null, $slidingPaginatorSubscriber, $repository = null, $locale = 'en') {

        $this->model = $model;
        $this->filter = $filter;
        $this->paginator = $paginator;
        $this->defaultSortOptions = $defaultSortOptions;
        $this->slidingPaginatorSubscriber = $slidingPaginatorSubscriber;
        $this->repository = $repository;
        $this->locale = $locale;
    }

    protected function getSnakeCase($text) {
        return str_replace("-", "_", ltrim(strtolower(preg_replace(self::REPLACE_MASK, '_$0', $text)), '_'));
    }

    public function isFilterUsed($driver, $request) {

        $fitlerPrefix = $this->getSnakeCase(sprintf('%s_%s_filter', $driver->getModule(), $driver->getAlias()));
        $context = $request->query->get('context');
        $fitlerParameters = $request->query->get($fitlerPrefix);

        $fitlerParametersCount = is_array($fitlerParameters) ? count($fitlerParameters) : 0;
        $contextCount = is_array($context) ? count($context) : 0;

        if ($fitlerParametersCount && $fitlerParametersCount >= $contextCount && (!$contextCount || count(array_diff_key($fitlerParameters, $context)) > 0)) {

            return true;
        }

        return false;
    }

    protected function filterQuery($form, $queryBuilder) {

        if ($form && $form->isSubmitted()) {

            $formMethod = $form->getConfig()->getMethod();
            return $this->filter->filter($form, $queryBuilder);
        }

        return $queryBuilder;
    }

    protected function getPrefix($driver) {

        return $this->getSnakeCase(sprintf('%s_%s', $driver->getModule(), $driver->getAlias()));
    }

    protected function getPageParameterName($prefix) {

        return $pageParameterName = sprintf('%s_page', $prefix);
    }

    protected function getPage($request, $pageParameterName) {

        return $page = $request->query->getInt($pageParameterName, 1);
    }

    protected function getSortFieldParameterName($prefix) {

        return sprintf('%s_sort', $prefix);
    }

    protected function getSortDirectionParameterName($prefix) {

        return sprintf('%s_direction', $prefix);
    }

    protected function getQueryBuilder($entityClass) {
        return $this->model->getQueryBuilder($entityClass);
    }

    protected function getSortOptions($prefix) {
        $sortFieldParameterName = $this->getSortFieldParameterName($prefix);
        $sortDirectionParameterName = $this->getSortDirectionParameterName($prefix);

        $options = [
            'sortFieldParameterName' => $sortFieldParameterName,
            'sortDirectionParameterName' => $sortDirectionParameterName,
        ];

        return $options;
    }

    protected function setDefaultSortValues($request, $sortFieldParameterName, $sortDirectionParameterName) {
        if (!$request->query->get($sortFieldParameterName) or ! $request->query->get($sortDirectionParameterName)) {

            if ($this->defaultSortOptions) {

                $_GET[$sortFieldParameterName] =  array_key_exists('sort', $this->defaultSortOptions) ? $this->defaultSortOptions['sort'] : 'entity.id';
                $_GET[$sortDirectionParameterName] =  array_key_exists('sortDirection', $this->defaultSortOptions) ? $this->defaultSortOptions['sortDirection'] : 'desc';
                
                
            } else {
                $_GET[$sortFieldParameterName] = 'entity.id';
                $_GET[$sortDirectionParameterName] = 'desc';
            }
        }
    }

    protected function paginate($queryBuilder, $page, $options) {
        return $this->paginator->paginate($queryBuilder, $page, null, $options);
    }

    protected function prepareQuery($entityClass, $request, $form, $queryBuilder, $driver) {
        return $this->filterQuery($form, $queryBuilder);
    }

    public function find($entityClass, $request = null, $form = null, $driver = null) {

        $queryBuilder = $this->repository->getConfiguredQueryBuilder();



        $this->prepareQuery($entityClass, $request, $form, $queryBuilder, $driver);
        $prefix = $this->getPrefix($driver);
        $pageParameterName = $this->getPageParameterName($prefix);
        $page = $this->getPage($request, $pageParameterName);

        $baseOptions = [];
        $baseOptions['pageParameterName'] = $pageParameterName;
        $baseOptions['filtered'] = $this->isFilterUsed($driver, $request);
        $options = array_merge($baseOptions, $this->getSortOptions($prefix));


        $this->setDefaultSortValues($request, $options['sortFieldParameterName'], $options['sortDirectionParameterName']);
        $this->slidingPaginatorSubscriber->loadParams($request);
        $queryBuilder->getQuery()
        ->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        )
        ->setHint(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $this->locale);
        $data = $this->paginate($queryBuilder, $page, $options);

        return $data;
    }

}
