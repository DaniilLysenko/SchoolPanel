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
            ->setParameter('q', '%'.$search->getName().'%')->getQuery()->getResult();
    }
}
