<?php

namespace App\Repository;

use App\Entity\TodoList;
use App\Entity\TodoListItem;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TodoListItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method TodoListItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method TodoListItem[]    findAll()
 * @method TodoListItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TodoListItemRepository extends ServiceEntityRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, TodoListItem::class);
        $this->manager = $manager;
    }

    /**
     * @param TodoList $todoList
     * @param string $name
     * @param string $description
     * @param bool $is_checked
     * @return TodoListItem
     */
    public function create(TodoList $todoList, string $name, string $description, bool $is_checked): TodoListItem
    {
        $todoListItem = new TodoListItem();

        $todoListItem->setList($todoList);
        $todoListItem->setName($name);
        $todoListItem->setDescription($description);
        $todoListItem->setIsChecked($is_checked);
        $todoListItem->setCreatedAt(new DateTime());

        $this->manager->persist($todoListItem);
        $this->manager->flush();

        return $todoListItem;
    }

    /**
     * @param TodoListItem $todoListItem
     * @return TodoListItem
     */
    public function update(TodoListItem $todoListItem): TodoListItem
    {
        $todoListItem->setUpdatedAt(new DateTime());

        $this->manager->persist($todoListItem);
        $this->manager->flush();

        return $todoListItem;
    }

    /**
     * @param TodoListItem $todoListItem
     */
    public function remove(TodoListItem $todoListItem): void
    {
        // TODO: Implement database not remove but mark as removed?
        $this->manager->remove($todoListItem);
        $this->manager->flush();
    }

    /**
     * @param $value
     * @return TodoListItem|null
     * @throws NonUniqueResultException
     */
    public function findOneById($value): ?TodoListItem
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

}
