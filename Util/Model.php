<?php

namespace Flexix\ModelBundle\Util;

use Flexix\ModelBundle\Util\ModelInterface;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Model implements ModelInterface {

    protected $entityManager;
    protected $holdFlush=false;

    public function __construct($entityManager) {
        $this->entityManager = $entityManager;
    }

    public function find($entityClass, $request=null, $form=null, $driver=null) {
        return $this->entityManager->getRepository($entityClass)->findAll();
    }

    public function findOneById($entityClass, $id) {
        return $this->entityManager->getRepository($entityClass)->findOneById($id);
    }

    public function getRepository($entityClass) {
        return $this->entityManager->getRepository($entityClass);
    }

    public function getQueryBuilder($entityClass) {
        return $this->entityManager->getRepository($entityClass)->createQueryBuilder('p');
    }
    
    public function setHoldFlush($holdFlush)
    {
        $this->holdFlush=$holdFlush;
    }
    
    public function flush()
    {
        if(!$this->holdFlush){
        $this->entityManager->flush();
        }
    }
    

    public function save($entity) {
        $this->entityManager->persist($entity);
        $this->flush();
        return $entity;
    }

    public function saveEntitites($arrayCollection) {

        foreach ($arrayCollection as $entity) {
            $this->entityManager->persist($entity);
        }
        $this->flush();
    }

    public function update() {
        $this->flush();
        return true;
    }

    public function delete($entity) {
        $this->entityManager->remove($entity);
        $this->flush();
        return true;
         
    }

}
