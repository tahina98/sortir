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

   /* public function findByNom(Filtre $filtre)
    {
        return $this->createQueryBuilder('s')
            ->select('s')
            ->andWhere('s.nom LIKE :nom')
            ->setParameter('nom',"%{$filtre->getNom()}%")
            ->getQuery()
            ->getResult();

    }

    public function findBySite(Filtre $filtre)
    {
        return $this
            ->createQueryBuilder('s')
            ->andWhere('s.site IN (:site)')
            ->setParameter('site',$filtre->getSite())
            ->getQuery()
            ->getResult();


    }

    public function findByDateHeureDebut(Filtre $filtre)
    {
        return $this
            ->createQueryBuilder('s')
            ->andWhere('s.dateHeureDebut >= :date')
            ->setParameter('date', $filtre->getDateHeureDebut())
            ->getQuery()
            ->getResult();
    }

    public function findByDateHeureFin(Filtre $filtre)
    {
        return $this
            ->createQueryBuilder('s')
            ->andWhere('s.dateHeureDebut <= :date')
            ->setParameter('date', $filtre->getDateHeureFin())
            ->getQuery()
            ->getResult();

    }

    public function findByOrganisateur(Participant $participant)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.organisateur = :organisateur')
            ->setParameter('organisateur', $participant)
            ->getQuery()
            ->getResult();
    }

    public function findByInscrit(Participant $participant)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.participants = :participants')
            ->setParameter('participants', $participant)
            ->getQuery()
            ->getResult();
    }

    public function findByDatePassee()
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.etat = 2')
            ->getQuery()
            ->getResult();
    }*/

    public function findFiltre(Filtre $filtre, Participant $participant):Paginator{
        $query= $this
            ->createQueryBuilder('s');
        if (!empty($filtre->getNom())){
            $query = $query
                ->andWhere('s.nom LIKE :nom')
                ->setParameter('nom',"%{$filtre->getNom()}%");
        }
        if (!empty($filtre->getSite())){
            $query = $query
                ->andWhere('s.site IN (:site)')
                ->setParameter('site',$filtre->getSite());
        }
        if (!empty($filtre->getDateHeureDebut())){
            $query = $query
                ->andWhere('s.dateHeureDebut >= :date')
                ->setParameter('date', $filtre->getDateHeureDebut());
        }
        if (!empty($filtre->getDateHeureFin())){
            $query = $query
                ->andWhere('s.dateHeureDebut <= :date')
                ->setParameter('date', $filtre->getDateHeureFin());
        }
        if (!empty($filtre->isOrganisateur())){
            $query = $query
                ->andWhere('s.organisateur = :organisateur')
                ->setParameter('organisateur', $participant);
        }
        //pas sur
        if (!empty($filtre->isInscrit())){
            $query = $query
                ->andWhere('s.participants = :participants')
                ->setParameter('participants', $participant);
        }
        if (!empty($filtre->isDatePassee())){
            $query = $query
                ->andWhere('s.etat == 2');
        }

        return new Paginator($query);

    }
}
