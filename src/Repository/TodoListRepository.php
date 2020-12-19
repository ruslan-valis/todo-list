<?php

namespace App\Repository;

use App\Entity\TodoList;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TodoList|null find($id, $lockMode = null, $lockVersion = null)
 * @method TodoList|null findOneBy(array $criteria, array $orderBy = null)
 * @method TodoList[]    findAll()
 * @method TodoList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TodoListRepository extends ServiceEntityRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, TodoList::class);
        $this->manager = $manager;
    }

    /**
     * @param string $name
     * @param string $description
     * @return TodoList
     */
    public function create(string $name, string $description): TodoList
    {
        $todoList = new TodoList();

        $todoList->setName($name);
        $todoList->setDescription($description);
        $todoList->setCreatedAt(new DateTime());

        $this->manager->persist($todoList);
        $this->manager->flush();

        return $todoList;
    }

    /**
     * @param TodoList $todoList
     * @return TodoList
     */
    public function update(TodoList $todoList): TodoList
    {
        $todoList->setUpdatedAt(new DateTime());

        $this->manager->persist($todoList);
        $this->manager->flush();

        return $todoList;
    }

    /**
     * @param TodoList $todoList
     */
    public function remove(TodoList $todoList): void
    {
        // TODO: Implement database not remove but mark as removed?
        $this->manager->remove($todoList);
        $this->manager->flush();
    }

    /**
     * @param $value
     * @return TodoList|null
     * @throws NonUniqueResultException
     */
    public function findOneById($value): ?TodoList
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
