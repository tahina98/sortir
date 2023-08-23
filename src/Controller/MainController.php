<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Bundle\MigrationsBundle;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class MainController extends AbstractController
{
    #[Route('/', name: 'main_home')]
    public function home(): Response
    {
        return $this->render('main/index.html.twig');
    }

    #[Route('/profil', name: 'main_profil')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    public function profil(): Response
    {
        return $this->render('main/profil.html.twig');
    }

    #[Route('/profil/modifier', name: 'main_modifProfil')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    public function modifProfil(
        EntityManagerInterface $em,
        Request $requete
    ): Response
    {
        $participant = $this->getUser();
        $participantForm =  $this->createForm(ProfilType::class, $participant);
        $participantForm->handleRequest($requete);

        if($participantForm->isSubmitted() && $participantForm->isValid()){
            $em->persist($participant);
            $em->flush();
            return $this->redirectToRoute('main_profil');
        }
        return $this->render('main/modifProfil.html.twig', [
            'participantForm'=>$participantForm->createView()
        ]);
    }
}
