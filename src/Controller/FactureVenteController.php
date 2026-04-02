<?php

namespace App\Controller;

use App\Entity\FactureVente;
use App\Form\FactureVenteType;
use App\Repository\FactureVenteRepository;
use App\Service\FactureVenteService;
use Dompdf\Dompdf;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/factures/vente')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class FactureVenteController extends AbstractController
{
    #[Route('', name: 'app_facture_vente_index', methods: ['GET'])]
    public function index(Request $request, FactureVenteRepository $repository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate($repository->createQueryBuilder('f')->orderBy('f.dateVente', 'DESC'), $request->query->getInt('page', 1), 10);
        return $this->render('facture_vente/index.html.twig', ['pagination' => $pagination]);
    }

    #[Route('/new', name: 'app_facture_vente_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FactureVenteService $service): Response
    {
        $facture = new FactureVente();
        $form = $this->createForm(FactureVenteType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service->save($facture);
            $this->addFlash('success', 'Facture vente créée.');
            return $this->redirectToRoute('app_facture_vente_index');
        }

        return $this->render('facture_vente/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}', name: 'app_facture_vente_show', methods: ['GET'])]
    public function show(FactureVente $facture): Response
    {
        return $this->render('facture_vente/show.html.twig', ['facture' => $facture]);
    }

    #[Route('/{id}/pdf', name: 'app_facture_vente_pdf', methods: ['GET'])]
    public function pdf(FactureVente $facture): Response
    {
        $dompdf = new Dompdf();
        $html = $this->renderView('facture_vente/pdf.html.twig', ['facture' => $facture]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="facture-vente-' . $facture->getId() . '.pdf"',
        ]);
    }
}
