<?php

namespace App\Repository;

use App\Entity\Etat;
use App\Entity\Filtre;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\QueryBuilder;
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

    private const JOURS_ARCHIVE = 30;
    const ARCHIVE = 5;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function findFiltre(Filtre $filtre, Participant $participant):Paginator{
        $query= $this
            ->createQueryBuilder('s');
        if (!empty($filtre->getNom())) {
            $query = $query
                ->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', "%{$filtre->getNom()}%");
        }
        if (!empty($filtre->getSite())) {

            $query = $query
                ->andWhere('s.site IN (:site)')
                ->setParameter('site', $filtre->getSite());
        }

       /* if ((!empty($filtre->getDateHeureDebut()))||(!empty($filtre->getDateHeureFin()))){
            $query=$query
                ->andWhere('s.dateHeureDebut BETWEEN :date1 AND :date2')
                ->setParameter('date1', $filtre->getDateHeureDebut())
                ->setParameter('date2',$filtre->getDateHeureFin());
        }*/
        if (!empty($filtre->getDateHeureDebut())) {
            $query = $query
                ->andWhere('s.dateHeureDebut >= :datedebut')
                ->setParameter('datedebut', $filtre->getDateHeureDebut());
        }
        if (!empty($filtre->getDateHeureFin())) {
            $query = $query
                ->andWhere('s.dateHeureDebut <= :datefin')
                ->setParameter('datefin', $filtre->getDateHeureFin());
        }

        if ($filtre->isOrganisateur()) {

            $query = $query
                ->andWhere('s.organisateur = :organisateur')
                ->setParameter('organisateur', $participant);

        }
        //fonctionnalité pas faite mais fonctionnel
         if ($filtre->isInscrit()){
             $query = $query
                 ->andWhere(':participants MEMBER OF s.participants')
                 ->setParameter('participants', $participant);
         }
        if ($filtre->isDatePassee()) {
            $query = $query
                ->andWhere('s.etat = 2');
        }

        return new Paginator($query);


    }


    public function findEnCoursCreation()
    {
        return $this
            ->createQueryBuilder('s')
            ->andWhere('s.etat = 1')
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws \Exception
     */
    public function archivageSorties()
    {
        $this
            ->createQueryBuilder('s')
            ->update(Sortie::class, 's')
            ->set('s.etat', ':valeur')
            ->where('s.dateHeureDebut < :valeur_condition')
            ->setParameter('valeur', '7')
            ->setParameter('valeur_condition', new \DateTime('-30 days'))
            ->getQuery()->execute();
       
    }


}
