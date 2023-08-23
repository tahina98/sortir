<?php

namespace App\Controller;

use App\Entity\Filtre;
use App\Entity\Sortie;
use App\Form\FiltreType;
use App\Form\SortieType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sorties', name: 'sortie')]
class SortieController extends AbstractController
{
    #[Route('/', name: '_liste')]
    //TODO Intégrer les rôles
    //TODO  l'affichage par site / isncrit ou non / organisateur ou non /sorties passées
        // TODO filtrer par date / nom de la sortie contient
    public function liste(SortieRepository $sortieRepository, Request $requete, ParticipantRepository $participantRepository): Response
    {
        $filtre=new Filtre();
        $filtreForm = $this->createForm(FiltreType::class, $filtre);
        $filtreForm->handleRequest($requete);
        $sorties=[];

        if ($filtreForm->isSubmitted() ){
            $filtre->setOrganisateur(true);

            if ($filtre->getNom()){
                $sorties = $sortieRepository->findByNom($filtre);
            }
            if ($filtre->getSite()){

                $sorties=$sortieRepository->findBySite($filtre);
            }

            if ($filtre->getDateHeureDebut()){
                $sorties=$sortieRepository->findByDateHeureDebut($filtre);
            }

            if ($filtre->getDateHeureFin()){
                $sorties=$sortieRepository->findByDateHeureFin($filtre);
            }


            if ($filtre->isOrganisateur()){

                $utilisateur = $participantRepository->findOneBy(["pseudo"=>$this->getUser()->getUserIdentifier()]);
                $sorties=$sortieRepository->findByOrganisateur($utilisateur);
            }



            if ($filtre->isInscrit()){
                $utilisateur = $participantRepository->findOneBy(["pseudo"=>$this->getUser()->getUserIdentifier()]);
                $sorties=$sortieRepository->findByInscrit($utilisateur);
            }
            /*
            if ($filtre->getDatePassee()){
                $sorties=$sortieRepository->findByDatePassee($filtre->getDatePassee());
            }*/
            return $this->render('sortie/liste.html.twig', compact('sorties','filtreForm'));
        }else{
            $sorties = $sortieRepository->findAll();
            $parSite="oui";
            return $this->render('sortie/liste.html.twig', compact('sorties','filtreForm','parSite'));
        }

    }

    #[Route('/creation', name: '_creation')]
    public function creation (
        EntityManagerInterface $entityManager,
        Request $requete
    ): Response
    {
        $sortie = new Sortie();
        $sortie->setOrganisateur($this->getUser());
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($requete);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()){
            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this->redirectToRoute('/sorties');
        }

        return $this->render('sortie/creation.html.twig',
            [
                "sortieForm"=>$sortieForm->createView()
            ]
        );
    }

    #[Route('/detail/{sortie}', name: '_detail')]
    public function detail (SortieRepository $sortieRepository, Sortie $sortie): Response
    {
        return $this->render('sortie/detail.html.twig', compact('sortie'));
    }
}
