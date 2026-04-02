<?php

namespace App\Controller;

use App\Entity\Grange;
use App\Form\GrangeType;
use App\Repository\GrangeRepository;
use App\Service\GrangeService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/granges')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class GrangeController extends AbstractController
{
    #[Route('', name: 'app_grange_index', methods: ['GET'])]
    public function index(Request $request, GrangeService $grangeService, GrangeRepository $grangeRepository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate($grangeRepository->createQueryBuilder('g')->orderBy('g.id', 'DESC'), $request->query->getInt('page', 1), 10);

        return $this->render('grange/index.html.twig', [
            'pagination' => $pagination,
            'stats' => $grangeService->getStats(),
        ]);
    }

    #[Route('/new', name: 'app_grange_new', methods: ['GET', 'POST'])]
    public function new(Request $request, GrangeService $service): Response
    {
        $grange = new Grange();
        $form = $this->createForm(GrangeType::class, $grange);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service->save($grange);
            $this->addFlash('success', 'Grange ajoutée.');
            return $this->redirectToRoute('app_grange_index');
        }

        return $this->render('grange/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}', name: 'app_grange_show', methods: ['GET'])]
    public function show(Grange $grange): Response
    {
        return $this->render('grange/show.html.twig', ['grange' => $grange]);
    }

    #[Route('/{id}/edit', name: 'app_grange_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Grange $grange, GrangeService $service): Response
    {
        $form = $this->createForm(GrangeType::class, $grange);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service->save($grange);
            $this->addFlash('success', 'Grange modifiée.');
            return $this->redirectToRoute('app_grange_index');
        }

        return $this->render('grange/edit.html.twig', ['form' => $form, 'grange' => $grange]);
    }

    #[Route('/{id}/delete', name: 'app_grange_delete', methods: ['POST'])]
    public function delete(Request $request, Grange $grange, GrangeService $service): Response
    {
        if ($this->isCsrfTokenValid('delete_grange_' . $grange->getId(), (string) $request->request->get('_token'))) {
            $service->delete($grange);
            $this->addFlash('success', 'Grange supprimée.');
        }

        return $this->redirectToRoute('app_grange_index');
    }
}
