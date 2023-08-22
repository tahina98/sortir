<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
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
    public function liste(SortieRepository $sortieRepository): Response
    {
        $sorties = $sortieRepository->findAll();
        return $this->render('sortie/liste.html.twig', compact('sorties'));
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

    #[Route('/detail/{$id}', name: '_detail', requirements:["id" => "\d+"])]
    public function detail (SortieRepository $sortieRepository, int $id=4): Response
    {
        dump($id);
        $sortie = $sortieRepository->findOneBy(
            ["id"=>$id]
        );
        return $this->render('sortie/detail.html.twig', compact('sortie'));
    }

}
