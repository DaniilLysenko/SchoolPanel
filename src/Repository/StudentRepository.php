<?php

namespace App\Repository;

use App\Entity\Student;
use App\Models\SearchModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Student|null find($id, $lockMode = null, $lockVersion = null)
 * @method Student|null findOneBy(array $criteria, array $orderBy = null)
 * @method Student[]    findAll()
 * @method Student[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Student::class);
    }

    public function studentSearch(SearchModel $search)
    {
        return $this->createQueryBuilder('s')
            ->select('s.id', 's.sex', 's.name', 's.age')
            ->where('s.name LIKE :q')
            ->setParameter('q', '%'.$search->getName().'%')
            ->getQuery()->getResult();
    }

    public function studentOrderSearch(SearchModel $search, $sort, $direction, $page, $perPage)
    {
        $offset = $page > 1 ? (($page - 1) * $perPage) : 0;
        return $this->createQueryBuilder('s')
            ->select('s.id', 's.sex', 's.name', 's.age')
            ->where('s.name LIKE :q')
            ->orderBy($sort, $direction)
            ->setFirstResult($offset)
            ->setMaxResults($perPage)
            ->setParameter('q', '%'.$search->getName().'%')->getQuery()->getResult();
    }

    public function studentOrder($sort, $direction, $page, $perPage)
    {
        $offset = $page * $perPage;
        return $this->createQueryBuilder('s')
            ->select('s.id', 's.sex', 's.name', 's.age')
            ->orderBy($sort, $direction)
            ->setFirstResult($offset)
            ->setMaxResults($perPage)->getQuery()->getResult();
    }

    public function studentFind()
    {
        return $this->createQueryBuilder('s')
            ->select('s.id', 's.sex', 's.name', 's.age')
            ->getQuery()->getResult();
    }

    public function countAllStudents()
    {
        return $this->createQueryBuilder('s')
            ->select('count(s.id)')
            ->getQuery()->getSingleScalarResult();
    }
}
