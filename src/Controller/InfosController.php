<?php

namespace App\Controller;

use App\Entity\Infos;
use App\Entity\Mouton;
use App\Form\InfosType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/moutons/{moutonId}/infos')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class InfosController extends AbstractController
{
    #[Route('/new', name: 'app_infos_new', methods: ['GET', 'POST'])]
    public function new(int $moutonId, Request $request, EntityManagerInterface $em): Response
    {
        $mouton = $em->getRepository(Mouton::class)->find($moutonId);
        if (!$mouton) {
            throw $this->createNotFoundException();
        }

        $infos = new Infos();
        $infos->setMouton($mouton);
        $form = $this->createForm(InfosType::class, $infos);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($infos);
            $em->flush();
            $this->addFlash('success', 'Information ajoutée.');
            return $this->redirectToRoute('app_mouton_show', ['id' => $moutonId]);
        }

        return $this->render('infos/new.html.twig', ['form' => $form, 'mouton' => $mouton]);
    }

    #[Route('/{id}/edit', name: 'app_infos_edit', methods: ['GET', 'POST'])]
    public function edit(int $moutonId, Infos $infos, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(InfosType::class, $infos);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Information modifiée.');
            return $this->redirectToRoute('app_mouton_show', ['id' => $moutonId]);
        }

        return $this->render('infos/edit.html.twig', ['form' => $form, 'moutonId' => $moutonId]);
    }

    #[Route('/{id}/delete', name: 'app_infos_delete', methods: ['POST'])]
    public function delete(int $moutonId, Infos $infos, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_infos_' . $infos->getId(), (string) $request->request->get('_token'))) {
            $em->remove($infos);
            $em->flush();
            $this->addFlash('success', 'Information supprimée.');
        }

        return $this->redirectToRoute('app_mouton_show', ['id' => $moutonId]);
    }
}
