<?php

namespace App\Aid\Controller;

use App\Aid\Entity\AidCampagne;
use App\Aid\Entity\AidDepense;
use App\Aid\Form\AidDepenseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/marketplace/aid')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class AidDepenseController extends AbstractController
{
    #[Route('/{campagneId}/depense/add', name: 'aid_depense_add', methods: ['POST'])]
    public function add(int $campagneId, Request $request, EntityManagerInterface $em): Response
    {
        $campagne = $em->getRepository(AidCampagne::class)->find($campagneId);
        if (!$campagne) {
            throw $this->createNotFoundException();
        }

        $depense = new AidDepense();
        $depense->setCampagne($campagne);
        $form = $this->createForm(AidDepenseType::class, $depense, [
            'action' => $this->generateUrl('aid_depense_add', ['campagneId' => $campagneId]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($depense);
            $em->flush();
            $this->addFlash('success', 'Dépense ajoutée.');
        } else {
            $this->addFlash('danger', 'Impossible d’ajouter la dépense.');
        }

        return $this->redirectToRoute('aid_campagne_show', ['id' => $campagneId]);
    }

    #[Route('/{campagneId}/depense/{id}/delete', name: 'aid_depense_delete', methods: ['GET'])]
    public function delete(int $campagneId, AidDepense $depense, EntityManagerInterface $em): Response
    {
        $em->remove($depense);
        $em->flush();

        $this->addFlash('success', 'Dépense supprimée.');

        return $this->redirectToRoute('aid_campagne_show', ['id' => $campagneId]);
    }
}
