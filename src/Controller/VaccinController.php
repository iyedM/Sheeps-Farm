<?php

namespace App\Controller;

use App\Entity\Mouton;
use App\Entity\Vaccin;
use App\Form\VaccinType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/moutons/{moutonId}/vaccins')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class VaccinController extends AbstractController
{
    #[Route('/new', name: 'app_vaccin_new', methods: ['GET', 'POST'])]
    public function new(int $moutonId, Request $request, EntityManagerInterface $em): Response
    {
        $mouton = $em->getRepository(Mouton::class)->find($moutonId);
        if (!$mouton) {
            throw $this->createNotFoundException();
        }

        $vaccin = new Vaccin();
        $vaccin->setMouton($mouton);
        $form = $this->createForm(VaccinType::class, $vaccin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($vaccin);
            $em->flush();
            $this->addFlash('success', 'Vaccin ajouté.');
            return $this->redirectToRoute('app_mouton_show', ['id' => $moutonId]);
        }

        return $this->render('vaccin/new.html.twig', ['form' => $form, 'mouton' => $mouton]);
    }

    #[Route('/{id}/edit', name: 'app_vaccin_edit', methods: ['GET', 'POST'])]
    public function edit(int $moutonId, Vaccin $vaccin, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(VaccinType::class, $vaccin);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Vaccin modifié.');
            return $this->redirectToRoute('app_mouton_show', ['id' => $moutonId]);
        }

        return $this->render('vaccin/edit.html.twig', ['form' => $form, 'moutonId' => $moutonId]);
    }

    #[Route('/{id}/delete', name: 'app_vaccin_delete', methods: ['POST'])]
    public function delete(int $moutonId, Vaccin $vaccin, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_vaccin_' . $vaccin->getId(), (string) $request->request->get('_token'))) {
            $em->remove($vaccin);
            $em->flush();
            $this->addFlash('success', 'Vaccin supprimé.');
        }

        return $this->redirectToRoute('app_mouton_show', ['id' => $moutonId]);
    }
}
