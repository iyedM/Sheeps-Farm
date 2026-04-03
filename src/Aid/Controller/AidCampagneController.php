<?php

namespace App\Aid\Controller;

use App\Aid\Entity\AidCampagne;
use App\Aid\Entity\AidDepense;
use App\Aid\Entity\AidLot;
use App\Aid\Form\AidCampagneType;
use App\Aid\Form\AidDepenseType;
use App\Aid\Form\AidLotType;
use App\Aid\Repository\AidCampagneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/marketplace/aid')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class AidCampagneController extends AbstractController
{
    #[Route('', name: 'aid_campagne_index', methods: ['GET'])]
    public function index(AidCampagneRepository $repository): Response
    {
        return $this->render('aid/campagnes/index.html.twig', [
            'campagnes' => $repository->findAllOrdered(),
        ]);
    }

    #[Route('/new', name: 'aid_campagne_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $campagne = new AidCampagne();
        $form = $this->createForm(AidCampagneType::class, $campagne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($campagne);
            $em->flush();

            $this->addFlash('success', 'Campagne créée avec succès.');

            return $this->redirectToRoute('aid_campagne_show', ['id' => $campagne->getId()]);
        }

        return $this->render('aid/campagnes/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'aid_campagne_show', methods: ['GET'])]
    public function show(AidCampagne $campagne): Response
    {
        $lotForm = $this->createForm(AidLotType::class, new AidLot(), [
            'action' => $this->generateUrl('aid_lot_add', ['campagneId' => $campagne->getId()]),
        ]);

        $depenseForm = $this->createForm(AidDepenseType::class, new AidDepense(), [
            'action' => $this->generateUrl('aid_depense_add', ['campagneId' => $campagne->getId()]),
        ]);

        return $this->render('aid/campagnes/show.html.twig', [
            'campagne' => $campagne,
            'lotForm' => $lotForm,
            'depenseForm' => $depenseForm,
        ]);
    }

    #[Route('/{id}/delete', name: 'aid_campagne_delete', methods: ['GET'])]
    public function delete(AidCampagne $campagne, EntityManagerInterface $em): Response
    {
        $em->remove($campagne);
        $em->flush();

        $this->addFlash('success', 'Campagne supprimée.');

        return $this->redirectToRoute('aid_campagne_index');
    }
}
