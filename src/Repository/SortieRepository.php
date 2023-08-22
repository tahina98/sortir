<?php

namespace App\Repository;

use App\Entity\Filtre;
use App\Entity\Site;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
    /*public function findBySite($value): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.site = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }*/

    public function findByNom(Filtre $filtre)
    {
        $query= $this->createQueryBuilder('s')
            ->select('s')
            ->andWhere('s.nom LIKE :nom')
            ->setParameter('nom',"%{$filtre->getNom()}%");
            return $paginator = new Paginator($query);

    }


//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findBySite(Filtre $site)
    {
        $query= $this->createQueryBuilder('t')

            ->andWhere('t.site IN (:site)')
            ->setParameter('site',$site->getSite())
            ->getQuery();

        $paginator = new Paginator($query);
        return $paginator;
    }
}
