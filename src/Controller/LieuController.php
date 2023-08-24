<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class LieuController extends AbstractController
{
    #[Route('/nouveauLieu', name: 'lieu_nouveau')]
    public function nouveau(
        EntityManagerInterface $entityManager,
        Request                $request,
    ): Response
    {
        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $lieuForm->handleRequest($request);


        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {
            $entityManager->persist($lieu);
            $entityManager->flush();
            return $this->redirectToRoute('sortie_creation');
        }
        return $this->render('lieu/nouveau.html.twig',
            [
                "lieuForm" => $lieuForm->createView()
            ]
        );
    }

}

