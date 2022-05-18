<?php

namespace App\Repository;

use App\DTO\SearchFilter;
use App\Entity\Job;
use App\Enum\JobOfferStatus;
use App\Enum\JobStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)
 * @method Job[]    findAll()
 * @method Job[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Job $entity, bool $flush = true): void
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
    public function remove(Job $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getById(int $id): Job
    {
        $job = $this->find($id);

        if ($job === null) {
            throw new \GeneralException('Skelbimas nerastas');
        }

        return $job;
    }

    public function getAllWithAddress(SearchFilter $searchFilter): array
    {
        $query = $this->createQueryBuilder('j')
            ->join('j.address', 'a');

        $expressions = [];

        if ($searchFilter->getCountryId() !== null) {
            $expressions[] = $query->expr()->eq('a.countryId', $searchFilter->getCountryId());
        }
        if ($searchFilter->getCityId() !== null) {
            $expressions[] = $query->expr()->eq('a.cityId', $searchFilter->getCityId());
        }
        if ($searchFilter->getStateId() !== null) {
            $expressions[] = $query->expr()->eq('a.stateId', $searchFilter->getStateId());
        }

        if (count($expressions) !== 0) {
            $query = $query->where($query->expr()->andX(...$expressions));
        }

        $query->andHaving(
            $query->expr()->in('j.status', [JobStatus::New->value, JobStatus::Pending->value])
        );

        $query->orderBy('id', 'desc');

        return $query->getQuery()->getResult();
    }

    /**
     * @param int $userId
     * @return Job[]
     */
    public function getAssignedJobs(int $userId): array
    {
        $q = $this->createQueryBuilder('j')
            ->join('j.jobOffer', 'jo');

        return $q->
            where($q->expr()->andX(
                $q->expr()->eq('jo.status', ':status'),
                $q->expr()->eq('jo.user_id', $userId),
        ))
            ->setParameter('status', JobOfferStatus::Accepted->value)
            ->orderBy('j.id', 'desc')
            ->getQuery()->getResult();
    }

    /**
     * @param int $userId
     * @return Job[]
     */
    public function getBuyersJobs(int $userId): array
    {
        return $this->findBy(['user_id' => $userId], ['id' => 'desc']);
    }

    /**
     * @return Job[]
     */
    public function getLast3Jobs(): array
    {
        $q = $this->createQueryBuilder('j')
            ->orderBy('j.id', 'desc')
            ->setMaxResults(3);

        return $q->getQuery()->getResult();
    }

    // /**
    //  * @return Job[] Returns an array of Job objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Job
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
