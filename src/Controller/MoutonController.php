<?php

namespace App\Controller;

use App\Entity\Mouton;
use App\Form\MoutonType;
use App\Service\MoutonService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/moutons')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class MoutonController extends AbstractController
{
    #[Route('', name: 'app_mouton_index', methods: ['GET'])]
    public function index(Request $request, MoutonService $service, PaginatorInterface $paginator): Response
    {
        $filters = [
            'race' => $request->query->get('race'),
            'genre' => $request->query->get('genre'),
            'origine' => $request->query->get('origine'),
            'ageMin' => $request->query->get('ageMin'),
            'ageMax' => $request->query->get('ageMax'),
            'dateFrom' => $request->query->get('dateFrom'),
            'dateTo' => $request->query->get('dateTo'),
        ];

        $pagination = $paginator->paginate($service->buildFilterQuery($filters), $request->query->getInt('page', 1), 10);

        return $this->render('mouton/index.html.twig', ['pagination' => $pagination, 'filters' => $filters]);
    }

    #[Route('/new', name: 'app_mouton_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MoutonService $service): Response
    {
        $mouton = new Mouton();
        $form = $this->createForm(MoutonType::class, $mouton);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service->save($mouton);
            $this->addFlash('success', 'Mouton ajouté avec succès.');
            return $this->redirectToRoute('app_mouton_index');
        }

        return $this->render('mouton/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}/edit', name: 'app_mouton_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Mouton $mouton, MoutonService $service): Response
    {
        $form = $this->createForm(MoutonType::class, $mouton);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service->save($mouton);
            $this->addFlash('success', 'Mouton modifié avec succès.');
            return $this->redirectToRoute('app_mouton_index');
        }

        return $this->render('mouton/edit.html.twig', ['form' => $form, 'mouton' => $mouton]);
    }

    #[Route('/{id}', name: 'app_mouton_show', methods: ['GET'])]
    public function show(Mouton $mouton): Response
    {
        return $this->render('mouton/show.html.twig', ['mouton' => $mouton]);
    }

    #[Route('/{id}/delete', name: 'app_mouton_delete', methods: ['POST'])]
    public function delete(Request $request, Mouton $mouton, MoutonService $service): Response
    {
        if ($this->isCsrfTokenValid('delete_mouton_' . $mouton->getId(), (string) $request->request->get('_token'))) {
            $service->delete($mouton);
            $this->addFlash('success', 'Mouton supprimé.');
        }

        return $this->redirectToRoute('app_mouton_index');
    }
}
