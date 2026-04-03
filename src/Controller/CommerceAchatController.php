<?php

namespace App\Controller;

use App\Entity\CommerceAchat;
use App\Form\CommerceAchatType;
use App\Repository\CommerceAchatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/achats')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class CommerceAchatController extends AbstractController
{
    #[Route('', name: 'app_commerce_achat_index', methods: ['GET'])]
    public function index(Request $request, CommerceAchatRepository $repository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate($repository->createQueryBuilder('a')->orderBy('a.dateAchat', 'DESC'), $request->query->getInt('page', 1), 10);
        return $this->render('commerce_achat/index.html.twig', ['pagination' => $pagination]);
    }

    #[Route('/new', name: 'app_commerce_achat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $achat = new CommerceAchat();
        $form = $this->createForm(CommerceAchatType::class, $achat);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($achat);
            
            // AUTOMATIC STOCKING: Create Mouton entries
            for ($i = 0; $i < $achat->getQuantite(); $i++) {
                $mouton = new \App\Entity\Mouton();
                $mouton->setRace($achat->getRace());
                $mouton->setAgeInitialMois($achat->getAge());
                $mouton->setGenre($achat->getGenre());
                $mouton->setPrix($achat->getPrixUnitaire());
                $mouton->setGrange($achat->getGrange());
                $mouton->setOrigine(\App\Entity\Mouton::ORIGINE_EXTERNE);
                $em->persist($mouton);
            }

            $em->flush();
            $this->addFlash('success', sprintf('%d moutons ajoutés au stock.', $achat->getQuantite()));
            return $this->redirectToRoute('app_commerce_achat_index');
        }
        return $this->render('commerce_achat/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}/edit', name: 'app_commerce_achat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CommerceAchat $achat, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CommerceAchatType::class, $achat);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Achat modifié.');
            return $this->redirectToRoute('app_commerce_achat_index');
        }
        return $this->render('commerce_achat/edit.html.twig', ['form' => $form, 'achat' => $achat]);
    }

    #[Route('/{id}/delete', name: 'app_commerce_achat_delete', methods: ['POST'])]
    public function delete(Request $request, CommerceAchat $achat, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_achat_' . $achat->getId(), (string) $request->request->get('_token'))) {
            $em->remove($achat);
            $em->flush();
            $this->addFlash('success', 'Achat supprimé.');
        }

        return $this->redirectToRoute('app_commerce_achat_index');
    }
}
