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
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/sorties', name: 'sortie')]
class SortieController extends AbstractController
{
    #[Route('/', name: '_liste')]
    public function liste(
        SortieRepository $sortieRepository,
        EtatRepository $etatRepository,
        Request $requete,
        ParticipantRepository $participantRepository): Response
    {
        //essaie n2
        if ($this->getUser()) {

            $filtre = new Filtre();
            $filtreForm = $this->createForm(FiltreType::class, $filtre);
            $filtreForm->handleRequest($requete);

            $utilisateur = $participantRepository->findOneBy(["pseudo" => $this->getUser()->getUserIdentifier()]);

            //compte le nombre de sorties au statut en création pour afficher la notif
            $sortiesUtilisateur = $utilisateur->getSortiesOrganisees();

            $nbAPublier = 0;
            foreach ($sortiesUtilisateur as $sortie){
                if ($sortie->getEtat() === $etatRepository->find(1)){
                    $nbAPublier = $nbAPublier+1;
                };
            }

            //compte le nombre de sorties annulées
            //depuis la dernière connexion
            $sortiesUtilisateur = $utilisateur->getSorties();
            $nbAnnulees = 0;
            $derniereConnexion = $utilisateur->getDerniereConnexion();
            foreach ($sortiesUtilisateur as $sortie){
                if ($sortie->getEtat() === $etatRepository->find(6) && $derniereConnexion < $sortie->getDateAnnulation()) {
                    $nbAnnulees = $nbAnnulees + 1;
                };
            }

            if ($filtreForm->isSubmitted()) {
                $sorties = $sortieRepository->findFiltre($filtre, $utilisateur);
                return $this->render('sortie/liste.html.twig', compact('sorties', 'filtreForm','nbAPublier', 'nbAnnulees'));


            }
            $sorties = $sortieRepository->findAll();
            return $this->render('sortie/liste.html.twig', compact('sorties', 'filtreForm','nbAPublier', 'nbAnnulees'));


        } else {
            $sorties = $sortieRepository->findAll();
            return $this->render('sortie/listeVisiteur.html.twig', compact('sorties'));
        }

    }

    #[Route('/listeapublier', name: '_liste_a_publier')]
    #[IsGranted('ROLE_USER')]
    public function liste_a_publier(SortieRepository $sortieRepository): Response
    {
        $sorties = $sortieRepository->findEnCoursCreation();
        return $this->render('sortie/listeEnCoursCreation.html.twig', compact('sorties'));
    }

    #[Route('/listeannule', name: '_liste_annule')]
    #[IsGranted('ROLE_USER')]
    public function liste_annule(SortieRepository $sortieRepository, ParticipantRepository $participantRepository): Response
    {
        $utilisateur = $participantRepository->findOneBy(["pseudo" => $this->getUser()->getUserIdentifier()]);
        $sorties = $sortieRepository->findAnnule($utilisateur);
        return $this->render('sortie/listeAnnule.html.twig', compact('sorties'));
    }


    #[Route('/publication/{sortie}', name: '_publier')]
    #[IsGranted('ROLE_USER')]
    public function publier(
        EntityManagerInterface $entityManager,
        SortieRepository       $sortieRepository,
        EtatRepository         $etatRepository,
        Request                $requete,
        Sortie                 $sortie
    ): Response
    {
        if ($this->getUser() === $sortie->getOrganisateur()){


        $sortie = $sortieRepository->find($sortie->getId());
        $etat = $etatRepository->findOneBy(['statutNom' => 'OUVERT']);
        $sortie->setEtat($etat);
        $entityManager->persist($sortie);
        $entityManager->flush();

        }
        return $this->redirectToRoute('sortie_liste');
    }

    #[Route('/annulation/{sortie}/{motif}', name: '_annuler')]
    #[IsGranted('ROLE_USER')]
    public function annuler(
        EntityManagerInterface $entityManager,
        EtatRepository         $etatRepository,
        Sortie                 $sortie,
        string $motif
    ): Response
    {
        if ($this->getUser() === $sortie->getOrganisateur()){
            $etat = $etatRepository->findOneBy(['statutNom' => 'ANNULE']);
            $sortie->setEtat($etat);
            $sortie->setMotifAnnulation($motif);
            $sortie->setDateAnnulation(new \DateTime());
            $entityManager->persist($sortie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sortie_liste');
    }

    #[Route('/suppression/{sortie}', name: '_supprimer')]
    #[IsGranted('ROLE_USER')]

    public function supprimer(
        EntityManagerInterface $entityManager,
        EtatRepository         $etatRepository,
        Sortie                 $sortie
    ): Response
    {
        if ($this->getUser() === $sortie->getOrganisateur()){
            $etat = $etatRepository->findOneBy(['statutNom' => 'ARCHIVE']);
            $sortie->setEtat($etat);
            $entityManager->persist($sortie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sortie_liste');
    }


    #[Route('/creation', name: '_creation')]
    #[IsGranted('ROLE_USER')]
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
    #[IsGranted('ROLE_USER')]

    public function detail(Sortie $sortie): Response
    {

        $etatSortie = $sortie->getEtat()->getStatutNom();

        if ($etatSortie === 'OUVERT' or $etatSortie === 'CLOTURE' or $etatSortie === 'EN_COURS' or $etatSortie === 'PASSE'){
            return $this->render('sortie/detail.html.twig', compact('sortie'));
        } elseif (($etatSortie === 'EN_CREATION' or $etatSortie ==='ANNULE' or $etatSortie ==='ARCHIVE') and $this->getUser() === $sortie->getOrganisateur()){
            return $this->render('sortie/detail.html.twig', compact('sortie'));

        }
        return $this->redirectToRoute('sortie_liste');
    }


    #[Route('/modification/{id}', name: '_modification')]
    #[IsGranted('ROLE_USER')]

    public function modification(
        EntityManagerInterface $entityManager,
        SortieRepository       $sortieRepository,
        EtatRepository         $etatRepository,
        Request                $requete,
        int                    $id
    ): Response
    {
        $sortie = $sortieRepository->find($id);
        $etatSortie = $sortie->getEtat()->getStatutNom();

        if ($this->getUser() === $sortie->getOrganisateur()
            and ($etatSortie === 'EN_CREATION'
            or $etatSortie === 'OUVERT'))
        {

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

        return $this->redirectToRoute('sortie_liste');

    }

    #[Route('/inscription/{sortie}', name: '_inscription')]
    #[IsGranted('ROLE_USER')]
    public function inscription(
        Sortie                 $sortie,
        EtatRepository         $etatRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $sortie->addParticipant($this->getUser());

        if ($sortie->getParticipants()->count() === $sortie->getNbInscriptionsMax()) {
            $etat = $etatRepository->find(4);
            $sortie->setEtat($etat);
        }

        $entityManager->persist($sortie);
        $entityManager->flush();


        return $this->redirectToRoute('sortie_liste');
    }


    #[Route('/desistement/{sortie}', name: '_desistement')]
    #[IsGranted('ROLE_USER')]

    public function desistement(
        Sortie                 $sortie,
        EntityManagerInterface $entityManager,
        EtatRepository         $etatRepository
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
