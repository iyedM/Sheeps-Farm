<?php

namespace App\Controller;

use App\Entity\Depense;
use App\Form\DepenseType;
use App\Repository\DepenseRepository;
use App\Service\DepenseService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/depenses')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class DepenseController extends AbstractController
{
    #[Route('', name: 'app_depense_index', methods: ['GET'])]
    public function index(Request $request, DepenseRepository $repository, DepenseService $service, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate($repository->createQueryBuilder('d')->orderBy('d.date', 'DESC'), $request->query->getInt('page', 1), 10);

        return $this->render('depense/index.html.twig', [
            'pagination' => $pagination,
            'total' => $service->getTotal(),
        ]);
    }

    #[Route('/new', name: 'app_depense_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DepenseService $service): Response
    {
        $depense = new Depense();
        $form = $this->createForm(DepenseType::class, $depense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service->save($depense);
            $this->addFlash('success', 'Dépense ajoutée.');
            return $this->redirectToRoute('app_depense_index');
        }

        return $this->render('depense/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}/edit', name: 'app_depense_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Depense $depense, DepenseService $service): Response
    {
        $form = $this->createForm(DepenseType::class, $depense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service->save($depense);
            $this->addFlash('success', 'Dépense modifiée.');
            return $this->redirectToRoute('app_depense_index');
        }

        return $this->render('depense/edit.html.twig', ['form' => $form, 'depense' => $depense]);
    }

    #[Route('/{id}/delete', name: 'app_depense_delete', methods: ['POST'])]
    public function delete(Request $request, Depense $depense, DepenseService $service): Response
    {
        if ($this->isCsrfTokenValid('delete_depense_' . $depense->getId(), (string) $request->request->get('_token'))) {
            $service->delete($depense);
            $this->addFlash('success', 'Dépense supprimée.');
        }

        return $this->redirectToRoute('app_depense_index');
    }
}
