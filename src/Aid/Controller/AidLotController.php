<?php

namespace App\Aid\Controller;

use App\Aid\Entity\AidCampagne;
use App\Aid\Entity\AidLot;
use App\Aid\Form\AidLotType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/marketplace/aid')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class AidLotController extends AbstractController
{
    #[Route('/{campagneId}/lot/add', name: 'aid_lot_add', methods: ['POST'])]
    public function add(int $campagneId, Request $request, EntityManagerInterface $em): Response
    {
        $campagne = $em->getRepository(AidCampagne::class)->find($campagneId);
        if (!$campagne) {
            throw $this->createNotFoundException();
        }

        $lot = new AidLot();
        $lot->setCampagne($campagne);
        $form = $this->createForm(AidLotType::class, $lot, [
            'action' => $this->generateUrl('aid_lot_add', ['campagneId' => $campagneId]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($lot);
            $em->flush();
            $this->addFlash('success', 'Lot ajouté.');
        } else {
            $this->addFlash('danger', 'Impossible d’ajouter le lot.');
        }

        return $this->redirectToRoute('aid_campagne_show', ['id' => $campagneId]);
    }

    #[Route('/{campagneId}/lot/{id}/delete', name: 'aid_lot_delete', methods: ['GET'])]
    public function delete(int $campagneId, AidLot $lot, EntityManagerInterface $em): Response
    {
        $em->remove($lot);
        $em->flush();

        $this->addFlash('success', 'Lot supprimé.');

        return $this->redirectToRoute('aid_campagne_show', ['id' => $campagneId]);
    }
}
