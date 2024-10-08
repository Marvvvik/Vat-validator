<?php

namespace App\Repository;

use App\Entity\VatValidationResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VatValidationResult>
 *
 * @method VatValidationResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method VatValidationResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method VatValidationResult[]    findAll()
 * @method VatValidationResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VatValidationResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VatValidationResult::class);
    }

//    /**
//     * @return VatValidationResult[] Returns an array of VatValidationResult objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?VatValidationResult
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
