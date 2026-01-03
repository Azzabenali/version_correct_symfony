<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    //    /**
    //     * @return Event[] Returns an array of Event objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Event
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
public function findByFilters(?int $category, ?int $lieu, ?string $date, ?float $prix)
{
    $qb = $this->createQueryBuilder('e')
        ->leftJoin('e.category', 'c')
        ->leftJoin('e.lieu', 'l');

    if ($category !== null) {
        $qb->andWhere('c.id = :cat')
           ->setParameter('cat', $category);
    }

    if ($lieu !== null) {
        $qb->andWhere('l.id = :lieu')
           ->setParameter('lieu', $lieu);
    }

    if ($date !== null) {
        $qb->andWhere('DATE(e.date) = :date')
           ->setParameter('date', $date);
    }

    if ($prix !== null) {
        $qb->andWhere('e.prix <= :prix')
           ->setParameter('prix', $prix);
    }

    return $qb->getQuery()->getResult();
}


}
