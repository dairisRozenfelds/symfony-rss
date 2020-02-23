<?php

namespace App\Repository;

use App\Entity\EnglishCommonWord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method EnglishCommonWord|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnglishCommonWord|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnglishCommonWord[]    findAll()
 * @method EnglishCommonWord[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnglishCommonWordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnglishCommonWord::class);
    }
}
