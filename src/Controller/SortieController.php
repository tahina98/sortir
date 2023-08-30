<?php

namespace App\Controller;

use App\Entity\Filtre;
use App\Entity\Sortie;
use App\Form\FiltreType;
use App\Form\SortieType;
use App\Form\SuppressionType;
use App\Repository\EtatRepository;
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
        //essaie n2
        if ($this->getUser()) {
            $filtre = new Filtre();
            $filtreForm = $this->createForm(FiltreType::class, $filtre);
            $filtreForm->handleRequest($requete);
            $utilisateur = $participantRepository->findOneBy(["pseudo" => $this->getUser()->getUserIdentifier()]);
            $sorties = $sortieRepository->findFiltre($filtre, $utilisateur);
            return $this->render('sortie/liste.html.twig', compact('sorties', 'filtreForm'));
        } else {
            $sorties = $sortieRepository->findAll();
            return $this->render('sortie/listeVisiteur.html.twig', compact('sorties'));
        }
    }

    #[Route('/listeapublier', name: '_liste_a_publier')]
    public function liste_a_publier(SortieRepository $sortieRepository): Response
    {
        $sorties = $sortieRepository->findEnCoursCreation();
        return $this->render('sortie/listeEnCoursCreation.html.twig', compact('sorties'));
    }

    #[Route('/publication/{sortie}', name: '_publier')]
    public function publier(
        EntityManagerInterface $entityManager,
        SortieRepository       $sortieRepository,
        EtatRepository         $etatRepository,
        Request                $requete,
        Sortie                 $sortie
    ): Response
    {
        $sortie = $sortieRepository->find($sortie->getId());
        $etat = $etatRepository->findOneBy(['statutNom' => 'OUVERT']);
        $sortie->setEtat($etat);
        $entityManager->persist($sortie);
        $entityManager->flush();
        return $this->redirectToRoute('sortie_liste');
    }


    #[Route('/creation', name: '_creation')]
    public function creation(
        EntityManagerInterface $entityManager,
        Request                $requete,
        EtatRepository         $etatRepository
    ): Response
    {
        $sortie = new Sortie();
        $sortie->setOrganisateur($this->getUser());
        $dateParDefautSortie = new \DateTime('now + 2 days');
        $sortie->setDateHeureDebut($dateParDefautSortie);
        $sortie->setDateLimiteInscription(new \DateTime('now'));
        $etat = $etatRepository->findOneBy(['statutNom' => 'EN_CREATION']);
        $sortie->setEtat($etat);
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($requete);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $clicked = $requete->request->get('clicked');

            if ($clicked === 'creer') {
                $entityManager->persist($sortie);
                $entityManager->flush();
                return $this->redirectToRoute('sortie_liste');
            }
            return $this->redirectToRoute('sortie_liste');
        }
        return $this->render('sortie/creation.html.twig',
            [
                "sortieForm" => $sortieForm->createView()
            ]
        );
    }


    #[Route('/detail/{sortie}', name: '_detail')]
    public function detail(Sortie $sortie): Response
    {
        return $this->render('sortie/detail.html.twig', compact('sortie'));
    }


    #[Route('/modification/{id}', name: '_modification')]
    public function modification(
        EntityManagerInterface $entityManager,
        SortieRepository       $sortieRepository,
        EtatRepository         $etatRepository,
        Request                $requete,
        int                    $id
    ): Response
    {
        $sortie = $sortieRepository->find($id);
        $sortieForm = $this->createForm(SuppressionType::class, $sortie);
        $sortieForm->handleRequest($requete);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $clicked = $requete->request->get('clicked');
            if ($clicked === 'supprimer') {

                $entityManager->remove($sortie);
                $entityManager->flush();
            }
            if ($clicked === 'modifier') {

                $entityManager->persist($sortie);
                $entityManager->flush();
            }
            if ($clicked === 'annuler') {
                $etat = $etatRepository->findOneBy(['statutNom' => 'ANNULE']);
                $sortie->setEtat($etat);
                $entityManager->persist($sortie);
                $entityManager->flush();
                $this->redirectToRoute('sortie_liste');
            }
            if ($clicked === 'publier') {
                $etat = $etatRepository->findOneBy(['statutNom' => 'OUVERT']);
                $sortie->setEtat($etat);
                $entityManager->persist($sortie);
                $entityManager->flush();
            }
            if ($clicked === 'retour' && !($sortieForm->isValid())) {
                return $this->redirectToRoute('sortie_liste');
            }
            return $this->redirectToRoute('sortie_liste');
        }
        return $this->render('sortie/modification.html.twig', compact('sortieForm', 'sortie'));
    }

    #[Route('/inscription/{sortie}', name: '_inscription')]
    public function inscription(
        Sortie                 $sortie,
        EtatRepository $etatRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $sortie->addParticipant($this->getUser());

        if($sortie->getParticipants()->count() === $sortie->getNbInscriptionsMax()){
            $etat = $etatRepository->find(4);
            $sortie->setEtat($etat);
        }

        $entityManager->persist($sortie);
        $entityManager->flush();


        return $this->redirectToRoute('sortie_liste');
    }


    #[Route('/desistement/{sortie}', name: '_desistement')]
    public function desistement(
        Sortie                 $sortie,
        EntityManagerInterface $entityManager,
        EtatRepository $etatRepository
    ): Response
    {


        if ($sortie->getParticipants()->count() === $sortie->getNbInscriptionsMax()) {
            $etat = $etatRepository->findOneBy(['statutNom' => 'OUVERT']);
            $sortie->setEtat($etat);
        }
        $sortie->removeParticipant($this->getUser());
        $entityManager->persist($sortie);
        $entityManager->flush();

        return $this->redirectToRoute('sortie_liste');

    }
}
