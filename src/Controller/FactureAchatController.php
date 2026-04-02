<?php

namespace App\Controller;

use App\Entity\FactureAchat;
use App\Form\FactureAchatType;
use App\Repository\FactureAchatRepository;
use App\Service\FactureAchatService;
use Dompdf\Dompdf;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/factures/achat')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class FactureAchatController extends AbstractController
{
    #[Route('', name: 'app_facture_achat_index', methods: ['GET'])]
    public function index(Request $request, FactureAchatRepository $repository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate($repository->createQueryBuilder('f')->orderBy('f.dateAchat', 'DESC'), $request->query->getInt('page', 1), 10);
        return $this->render('facture_achat/index.html.twig', ['pagination' => $pagination]);
    }

    #[Route('/new', name: 'app_facture_achat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FactureAchatService $service): Response
    {
        $facture = new FactureAchat();
        $form = $this->createForm(FactureAchatType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service->save($facture);
            $this->addFlash('success', 'Facture achat créée.');
            return $this->redirectToRoute('app_facture_achat_index');
        }

        return $this->render('facture_achat/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}', name: 'app_facture_achat_show', methods: ['GET'])]
    public function show(FactureAchat $facture): Response
    {
        return $this->render('facture_achat/show.html.twig', ['facture' => $facture]);
    }

    #[Route('/{id}/pdf', name: 'app_facture_achat_pdf', methods: ['GET'])]
    public function pdf(FactureAchat $facture): Response
    {
        $dompdf = new Dompdf();
        $html = $this->renderView('facture_achat/pdf.html.twig', ['facture' => $facture]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="facture-achat-' . $facture->getId() . '.pdf"',
        ]);
    }
}
