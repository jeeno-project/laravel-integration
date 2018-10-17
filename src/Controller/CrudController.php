<?php
/**
 * Created by PhpStorm.
 * User: christian
 * Date: 7/14/17
 * Time: 1:01 PM
 */

namespace Jeeno\LaravelIntegration\Controller;

use Jeeno\Core\Exception\ResourceNotFoundException;
use Jeeno\Core\Exception\ValidationException;
use Jeeno\Core\Helper\ModelSerializer;
use Jeeno\Core\Entity\Entity;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class CrudController
 *
 * @package Http\Controllers\Rest
 */
abstract class CrudController
{
    protected const GET    = 'get';
    protected const LIST   = 'list';
    protected const CREATE = 'create';
    protected const UPDATE = 'update';
    protected const DELETE = 'create';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $repository;

    /**
     * @var ModelSerializer
     */
    private $serializer;

    /**
     * @var PropertyAccessor
     */
    private $accessor;

    /**
     * @var string
     */
    protected $operation;

    /**
     * CrudController constructor.
     *
     * @param EntityManager   $entityManager
     * @param ModelSerializer $serializer
     */
    public function __construct(EntityManager $entityManager, ModelSerializer $serializer)
    {
        $this->entityManager = $entityManager;
        $this->repository    = $entityManager->getRepository(static::getEntityClass());
        $this->serializer    = $serializer;

        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @return string
     */
    abstract static protected function getEntityClass(): string;

    /**
     * @return string
     */
    abstract static protected function getSlug(): string;

    public static function registerRoutes()
    {
        $slug = static::getSlug();

        Route::get("api/{$slug}/", static::class . '@getAll');//->middleware('cors');
        Route::get("api/{$slug}/{id}", static::class . '@get');//->middleware('cors');
        Route::post("api/{$slug}/", static::class . '@create');//->middleware('cors');
        Route::put("api/{$slug}/{id}", static::class . '@update');//->middleware('cors');
        Route::delete("api/{$slug}/{id}", static::class . '@delete');//->middleware('cors');
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAll(Request $request)
    {
        $this->operation = static::LIST;

        $orderBy = $request->get('_sort', 'id');
        $sort    = $request->get('_order', 'ASC');
        $start   = $request->get('_start', 0);
        $end     = $request->get('_end', 10);
        $query   = $request->get('q', null);
        $ids     = $request->get('ids', null);

        $limit = $end - $start;
        //$offset = ($start / $limit) + 1;

        $count = $this->getEntitiesCount();

        $qb = $this->repository->createQueryBuilder('o');

        $qb->select('o');

        if ($ids) {

            $indexes = explode(',', $ids);

            $this->validateListOfNumbers($indexes);

            $qb->add('where', $qb->expr()->in('o.id', $indexes));
            //            $qb->where('o.id IN (:ids)')
            //               ->setParameter('ids', $ids);
        }

        if ($query) {
            $qb->where('o.title LIKE :name')
               ->setParameter('name', "%{$query}%");
        }

        $qb->setMaxResults($limit)->setFirstResult($start);
        $qb->orderBy("o.{$orderBy}", $sort);

        $entities = $qb->getQuery()->getResult();

        $result = $this->serializeCollection($entities);

        return response()->json($result, 200, ['X-Total-Count' => $count]);
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws ResourceNotFoundException
     */
    public function get(int $id)
    {
        $this->operation = static::GET;

        /** @var Entity $entity */
        $entity = $this->repository->find($id);

        if (!$entity) {
            throw new ResourceNotFoundException(static::getEntityClass(), $id);
        }

        $result = $this->serializeOne($entity);

        return response()->json($result, 200, ['X-Total-Count' => 1]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \TypeError
     * @throws \ReflectionException
     */
    public function create(Request $request)
    {
        $this->operation = static::CREATE;

        $data = $request->json()->all();

        $entity = $this->createEntity();

        $this->save($entity, $data);

        $result = ['id' => $entity->getId()];

        return response()->json($result, 201);
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \TypeError
     */
    public function update(Request $request, int $id)
    {
        $this->operation = static::UPDATE;

        $data = $request->json()->all();

        /** @var Entity $entity */
        $entity = $this->repository->find($id);

        $this->save($entity, $data);

        $result = ['id' => $entity->getId()];

        return response()->json($result, 200);
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(int $id)
    {
        $this->operation = static::DELETE;

        $entity = $this->repository->find($id);

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        $data = ['id' => $entity->getId()];

        return response()->json($data, 200);
    }

    /**
     * @param Entity $entity
     * @param array  $data
     *
     * @throws \TypeError
     */
    protected function hydrate(Entity $entity, array &$data): void
    {
        //        print_r($data);
        //        die();

        foreach ($data as $key => $value) {
            if ($this->accessor->isWritable($entity, $key)) {
                $this->accessor->setValue($entity, $key, $value);
            }
        }
    }

    /**
     * @return array
     */
    protected function getValidationRules(): array
    {
        return [];
    }

    /**
     * @return Entity
     *
     * @throws \ReflectionException
     */
    private function createEntity(): Entity
    {
        $rc = new \ReflectionClass(static::getEntityClass());

        /** @var Entity $entity */
        $entity = $rc->newInstanceWithoutConstructor();

        return $entity;
    }

    /**
     * @return int
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function getEntitiesCount(): int
    {
        $qb = $this->repository->createQueryBuilder('o');

        $qb->select('count(o)');

        $count = $qb->getQuery()->getSingleScalarResult();

        return $count;
    }

    /**
     * @param Entity $entity
     * @param array  $data
     */
    protected function afterHydration(Entity $entity, array &$data)
    {

    }

    /**
     * @param Entity $entity
     *
     * @return array
     */
    protected function serializeOne(Entity $entity): array
    {
        $data = [];

        $this->beforeSerialization($entity, $data);

        $result = $this->serializer->serialize($entity);

        $result = array_merge($result, $data);

        $this->afterSerialization($entity, $result);

        return $result;
    }

    /**
     * @param array $entities
     *
     * @return array
     */
    protected function serializeCollection(array $entities): array
    {
        $result = [];

        foreach ($entities as $entity) {
            $result[] = $this->serializeOne($entity);
        }

        return $result;
    }

    /**
     * @param Entity $entity
     * @param array  $data
     */
    protected function beforeHydration(Entity $entity, array &$data)
    {

    }

    /**
     * @param Entity $entity
     * @param array  $data
     */
    protected function beforeSerialization(Entity $entity, array &$data)
    {

    }

    /**
     * @param Entity $entity
     * @param array  $data
     */
    protected function afterSerialization(Entity $entity, array &$data)
    {

    }

    /**
     * @param       $entity
     * @param array $data
     *
     * @throws ValidationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \TypeError
     */
    private function save($entity, array &$data)
    {
        try {
            $this->beforeHydration($entity, $data);
            $this->hydrate($entity, $data);
            $this->afterHydration($entity, $data);

            $this->beforeSave($entity);
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
            $this->afterSave($entity);

        } catch (UniqueConstraintViolationException $exception) {
            throw new ValidationException(class_basename(static::getEntityClass()), 'Duplicate primary key');
        }
    }

    /**
     * @param Entity $entity
     */
    protected function afterSave(Entity $entity)
    {

    }

    /**
     * @param Entity $entity
     */
    protected function beforeSave(Entity $entity)
    {

    }

    /**
     * @param array $numbers
     */
    private function validateListOfNumbers(array &$numbers)
    {
        foreach ($numbers as $number) {
            if (!is_numeric($number) || !is_integer((int)$number)) {
                throw new \InvalidArgumentException('Invalid indexes');
            }
        }
    }
}
