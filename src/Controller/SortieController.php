<?php

namespace App\Controller;

use App\Entity\Filtre;
use App\Entity\Sortie;
use App\Form\FiltreType;
use App\Form\SortieType;
use App\Form\SuppressionType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\Clock\now;

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
        $filtre =new Filtre();
        $filtreForm = $this->createForm(FiltreType::class, $filtre);
        $filtreForm->handleRequest($requete);
        $utilisateur = $participantRepository->findOneBy(["pseudo"=>$this->getUser()->getUserIdentifier()]);
        $sorties=$sortieRepository->findFiltre($filtre,$utilisateur);
        return $this->render('sortie/liste.html.twig', compact('sorties','filtreForm'));

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
            return $this->redirectToRoute('sortie_liste');
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



    #[Route('/modification/suppression/{id}', name: '_suppression')]
    public function suppression (
        EntityManagerInterface $entityManager,
        SortieRepository $sortieRepository,
        Request $requete,
        int $id
    ): Response
    {
        $sortie=$sortieRepository->find($id);
        $sortieForm = $this->createForm(SuppressionType::class, $sortie);
        $sortieForm->handleRequest($requete);

        if ($sortieForm->isSubmitted() ){

            $clicked = $requete->request->get('clicked');
           if ($clicked ==='supprimer') {

                $entityManager->remove($sortie);
                $entityManager->flush();
            }
            if ($clicked ==='modifier') {

                $entityManager->persist($sortie);
                $entityManager->flush();
            }
               return $this->redirectToRoute('sortie_liste');
        }
        return $this->render('sortie/suppression.html.twig', compact('sortieForm','sortie'));

    #[Route('/inscription/{sortie}', name: '_inscription')]
    public function inscription (
        Sortie $sortie,
        EntityManagerInterface $entityManager
    ): Response
    {

        $sortie->addParticipant($this->getUser());
        $entityManager->persist($sortie);
        $entityManager->flush();

            return $this->redirectToRoute('sortie_liste');
    }

    #[Route('/desistement/{sortie}', name: '_desistement')]
    public function desistement (
        Sortie $sortie,
        EntityManagerInterface $entityManager
    ): Response
    {

        $sortie->removeParticipant($this->getUser());
        $entityManager->persist($sortie);
        $entityManager->flush();

        return $this->redirectToRoute('sortie_liste');

    }
}
