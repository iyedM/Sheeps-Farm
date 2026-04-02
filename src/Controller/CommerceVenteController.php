<?php

namespace App\Controller;

use App\Entity\CommerceVente;
use App\Form\CommerceVenteType;
use App\Repository\CommerceVenteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/ventes')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class CommerceVenteController extends AbstractController
{
    #[Route('', name: 'app_commerce_vente_index', methods: ['GET'])]
    public function index(Request $request, CommerceVenteRepository $repository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate($repository->createQueryBuilder('v')->orderBy('v.dateVente', 'DESC'), $request->query->getInt('page', 1), 10);
        return $this->render('commerce_vente/index.html.twig', ['pagination' => $pagination]);
    }

    #[Route('/new', name: 'app_commerce_vente_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $vente = new CommerceVente();
        $form = $this->createForm(CommerceVenteType::class, $vente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($vente);
            $em->flush();
            $this->addFlash('success', 'Vente ajoutée.');
            return $this->redirectToRoute('app_commerce_vente_index');
        }

        return $this->render('commerce_vente/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}/edit', name: 'app_commerce_vente_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CommerceVente $vente, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CommerceVenteType::class, $vente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Vente modifiée.');
            return $this->redirectToRoute('app_commerce_vente_index');
        }

        return $this->render('commerce_vente/edit.html.twig', ['form' => $form, 'vente' => $vente]);
    }

    #[Route('/{id}/delete', name: 'app_commerce_vente_delete', methods: ['POST'])]
    public function delete(Request $request, CommerceVente $vente, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_vente_' . $vente->getId(), (string) $request->request->get('_token'))) {
            $em->remove($vente);
            $em->flush();
            $this->addFlash('success', 'Vente supprimée.');
        }

        return $this->redirectToRoute('app_commerce_vente_index');
    }
}
