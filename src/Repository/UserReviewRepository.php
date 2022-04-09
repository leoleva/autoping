<?php

namespace App\Repository;

use App\Entity\Job;
use App\Entity\UserReview;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserReview|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserReview|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserReview[]    findAll()
 * @method UserReview[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserReview::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(UserReview $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(UserReview $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }


    public function getById(int $id): UserReview
    {
        $job = $this->find($id);

        if ($job === null) {
            throw new \GeneralException('Atsiliepimas nerastas');
        }

        return $job;
    }


    // /**
    //  * @return UserReview[] Returns an array of UserReview objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserReview
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
