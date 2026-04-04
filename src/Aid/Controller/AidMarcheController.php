<?php

namespace App\Aid\Controller;

use App\Aid\Entity\AidCampagne;
use App\Aid\Entity\AidMarche;
use App\Aid\Entity\AidMarcheLigne;
use App\Aid\Form\AidMarcheType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/marketplace/aid')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class AidMarcheController extends AbstractController
{
    #[Route('/{campagneId}/marche/new', name: 'aid_marche_new', methods: ['GET', 'POST'])]
    public function new(int $campagneId, Request $request, EntityManagerInterface $em): Response
    {
        $campagne = $this->findCampagne($em, $campagneId);
        $marche = new AidMarche();
        $marche->setCampagne($campagne);
        $marche->addLigne(new AidMarcheLigne());

        return $this->handleForm($request, $em, $campagne, $marche, 'aid/marches/new.html.twig', 'Marché créé.');
    }

    #[Route('/{campagneId}/marche/{id}/edit', name: 'aid_marche_edit', methods: ['GET', 'POST'])]
    public function edit(int $campagneId, AidMarche $marche, Request $request, EntityManagerInterface $em): Response
    {
        $campagne = $this->findCampagne($em, $campagneId);

        if ($marche->getLignes()->isEmpty()) {
            $marche->addLigne(new AidMarcheLigne());
        }

        return $this->handleForm($request, $em, $campagne, $marche, 'aid/marches/edit.html.twig', 'Marché modifié.');
    }

    #[Route('/{campagneId}/marche/{id}', name: 'aid_marche_show', methods: ['GET'])]
    public function show(int $campagneId, AidMarche $marche): Response
    {
        return $this->render('aid/marches/show.html.twig', [
            'campagne' => $marche->getCampagne(),
            'marche' => $marche,
        ]);
    }

    #[Route('/{campagneId}/marche/{id}/delete', name: 'aid_marche_delete', methods: ['GET'])]
    public function delete(int $campagneId, AidMarche $marche, EntityManagerInterface $em): Response
    {
        $em->remove($marche);
        $em->flush();

        $this->addFlash('success', 'Marché supprimé.');

        return $this->redirectToRoute('aid_campagne_show', ['id' => $campagneId]);
    }

    #[Route('/{campagneId}/marche/{id}/pdf', name: 'aid_marche_pdf', methods: ['GET'])]
    public function exportPdf(AidMarche $marche): Response
    {
        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        
        $html = $this->renderView('aid/marches/pdf.html.twig', [
            'marche' => $marche,
            'campagne' => $marche->getCampagne()
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="rapport-marche-' . $marche->getNom() . '.pdf"'
        ]);
    }

    private function handleForm(Request $request, EntityManagerInterface $em, AidCampagne $campagne, AidMarche $marche, string $template, string $successMessage): Response
    {
        $form = $this->createForm(AidMarcheType::class, $marche, ['campagne' => $campagne]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $error = $this->validateLignes($marche, $campagne);
            if ($error !== null) {
                $this->addFlash('danger', $error);
            } else {
                $this->syncLignes($marche);
                $marche->recalculateTotals();
                $marche->setCampagne($campagne);
                $em->persist($marche);
                $em->flush();

                $this->addFlash('success', $successMessage);

                return $this->redirectToRoute('aid_marche_show', [
                    'campagneId' => $campagne->getId(),
                    'id' => $marche->getId(),
                ]);
            }
        }

        return $this->render($template, [
            'campagne' => $campagne,
            'marche' => $marche,
            'form' => $form,
        ]);
    }

    private function findCampagne(EntityManagerInterface $em, int $campagneId): AidCampagne
    {
        $campagne = $em->getRepository(AidCampagne::class)->find($campagneId);

        if (!$campagne) {
            throw $this->createNotFoundException();
        }

        return $campagne;
    }

    private function validateLignes(AidMarche $marche, AidCampagne $campagne): ?string
    {
        $usedByLot = [];

        foreach ($campagne->getLots() as $lot) {
            $usedByLot[$lot->getId()] = 0;
        }

        foreach ($campagne->getMarches() as $existingMarche) {
            if ($marche->getId() !== null && $existingMarche->getId() === $marche->getId()) {
                continue;
            }

            foreach ($existingMarche->getLignes() as $ligne) {
                $lotId = $ligne->getLot()?->getId();
                if ($lotId !== null) {
                    $usedByLot[$lotId] = ($usedByLot[$lotId] ?? 0) + $ligne->getQuantiteAmenes();
                }
            }
        }

        $seenLotIds = [];

        foreach ($marche->getLignes() as $ligne) {
            $lot = $ligne->getLot();
            if (!$lot) {
                return 'Chaque ligne doit sélectionner un lot.';
            }

            $lotId = $lot->getId();
            if (in_array($lotId, $seenLotIds, true)) {
                return 'Un lot ne peut apparaître qu’une seule fois dans le même marché.';
            }
            $seenLotIds[] = $lotId;

            if ($ligne->getQuantiteAmenes() <= 0) {
                return 'La quantité amenée doit être positive pour chaque ligne.';
            }

            if ($ligne->getQuantiteVendus() < 0) {
                return 'La quantité vendue ne peut pas être négative.';
            }

            if ($ligne->getQuantiteVendus() > $ligne->getQuantiteAmenes()) {
                return 'Pour chaque ligne, la quantité vendue ne peut pas dépasser la quantité amenée.';
            }

            $remaining = $lot->getQuantite() - ($usedByLot[$lotId] ?? 0);
            if ($ligne->getQuantiteAmenes() > $remaining) {
                return sprintf('Le lot #%d ne dispose plus que de %d moutons.', $lotId, max(0, $remaining));
            }

            $usedByLot[$lotId] = ($usedByLot[$lotId] ?? 0) + $ligne->getQuantiteAmenes();
        }

        return null;
    }

    private function syncLignes(AidMarche $marche): void
    {
        foreach ($marche->getLignes() as $ligne) {
            $ligne->setMarche($marche);
        }
    }
}
