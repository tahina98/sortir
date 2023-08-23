<?php

namespace App\Repository;

use App\Entity\Filtre;
use App\Entity\Participant;
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
            return  new Paginator($query);

    }

    public function findBySite(Filtre $filtre)
    {
        $query = $this
            ->createQueryBuilder('s')
            ->andWhere('s.site IN (:site)')
            ->setParameter('site',$filtre->getSite())
            ->getQuery();

        return  new Paginator($query);


    }

    public function findByDateHeureDebut(Filtre $filtre)
    {
        $query = $this
            ->createQueryBuilder('s')
            ->andWhere('s.dateHeureDebut >= :date')
            ->setParameter('date', $filtre->getDateHeureDebut())
            ->getQuery();

        return  new Paginator($query);
    }

    public function findByDateHeureFin(Filtre $filtre)
    {
        $query = $this
            ->createQueryBuilder('s')
            ->andWhere('s.dateHeureDebut <= :date')
            ->setParameter('date', $filtre->getDateHeureFin())
            ->getQuery();

           return new Paginator($query);

    }

    public function findByOrganisateur(Participant $participant)
    {
        $querry= $this->createQueryBuilder('s')
            ->andWhere('s.organisateur = :organisateur')
            ->setParameter('organisateur', $participant)
            ->getQuery();

        $paginator = new Paginator($querry);
        return $paginator;
    }

    public function findByInscrit(Participant $participant)
    {
        $querry= $this->createQueryBuilder('s')
            ->andWhere('s.participants = :participants')
            ->setParameter('participants', $participant)
            ->getQuery();

        $paginator = new Paginator($querry);
        return $paginator;
    }


}
