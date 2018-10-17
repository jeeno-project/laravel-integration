<?php
/**
 * Created by PhpStorm.
 * User: christian
 * Date: 6/14/17
 * Time: 3:58 PM
 */

namespace Jeeno\LaravelIntegration\Repository;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository as DoctrineRepository;
use Jeeno\Core\Entity\Entity;
use Jeeno\Core\Repository\EntityRepository;

/**
 * Class DoctrineEntityRepository
 *
 * @package Jeeno\LaravelIntegration\Repository
 */
abstract class DoctrineEntityRepository implements EntityRepository
{
    /** @var  DoctrineRepository */
    protected $repository;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /**
     * DoctrineEntityRepository constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository    = $entityManager->getRepository($this->getEntityClass());
        $this->entityManager = $entityManager;
    }

    /**
     * @return string
     */
    abstract public function getEntityClass(): string;

    /**
     * @param int $id
     *
     * @return Entity|null
     */
    public function findById(int $id): ?Entity
    {
        /** @var Entity $entity */
        $entity = $this->repository->find($id);

        return $entity;
    }

    /**
     * @return Entity[]
     */
    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    /**
     * @param Entity $entity
     * @param bool   $flush
     */
    public function add(Entity $entity, bool $flush = true): void
    {
        $this->entityManager->persist($entity);

        if ($flush) {
            $this->flush();
        }
    }

    /**
     * @param Entity $entity
     * @param bool   $flush
     */
    public function remove(Entity $entity, bool $flush = true): void
    {
        $this->entityManager->remove($entity);

        if ($flush) {
            $this->flush();
        }
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}