<?php

namespace App\Controller;

use App\Entity\Infos;
use App\Entity\Mouton;
use App\Entity\Vaccin;
use App\Form\BulkVaccinType;
use App\Form\InfosType;
use App\Form\MoutonGroupType;
use App\Form\MoutonType;
use App\Form\VaccinType;
use App\Service\MoutonService;
use Doctrine\ORM\EntityManagerInterface;
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

    #[Route('/group/new', name: 'app_mouton_group_new', methods: ['GET', 'POST'])]
    public function newGroup(Request $request, MoutonService $service, EntityManagerInterface $em): Response
    {
        $moutonPrototype = new Mouton();
        $form = $this->createForm(MoutonGroupType::class, $moutonPrototype);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quantity = max(1, (int) $form->get('quantite')->getData());
            for ($index = 0; $index < $quantity; ++$index) {
                $mouton = new Mouton();
                $mouton->setRace((string) $form->get('race')->getData());
                $mouton->setGenre((string) $form->get('genre')->getData());
                $mouton->setAgeInitialMois((int) $form->get('ageInitialMois')->getData());
                $mouton->setDateAjout($form->get('dateAjout')->getData());
                $mouton->setGrange($form->get('grange')->getData());
                $mouton->setOrigine((string) $form->get('origine')->getData());
                $mouton->setPrix((float) $form->get('prix')->getData());
                $em->persist($mouton);
            }

            $em->flush();

            $this->addFlash('success', sprintf('%d moutons ont été créés avec succès.', $quantity));

            return $this->redirectToRoute('app_mouton_index');
        }

        return $this->render('mouton/group_new.html.twig', ['form' => $form]);
    }

    #[Route('/vaccins/bulk', name: 'app_mouton_vaccin_bulk', methods: ['GET', 'POST'])]
    public function bulkVaccin(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(BulkVaccinType::class, new Vaccin());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedMoutons = $form->get('moutons')->getData();
            $vaccinName = (string) $form->get('nom')->getData();
            $vaccinDate = $form->get('dateVaccination')->getData();

            $count = 0;
            foreach ($selectedMoutons as $selectedMouton) {
                $vaccin = new Vaccin();
                $vaccin->setNom($vaccinName);
                $vaccin->setDateVaccination($vaccinDate);
                $vaccin->setMouton($selectedMouton);
                $em->persist($vaccin);
                ++$count;
            }

            $em->flush();
            $this->addFlash('success', sprintf('Vaccin ajouté à %d moutons.', $count));

            return $this->redirectToRoute('app_mouton_index');
        }

        return $this->render('mouton/bulk_vaccin.html.twig', ['form' => $form]);
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

    #[Route('/{id}', name: 'app_mouton_show', methods: ['GET', 'POST'])]
    public function show(Mouton $mouton, Request $request, EntityManagerInterface $em): Response
    {
        $vaccin = (new Vaccin())->setMouton($mouton);
        $vaccinForm = $this->createForm(VaccinType::class, $vaccin, [
            'action' => $this->generateUrl('app_mouton_show', ['id' => $mouton->getId()]),
        ]);
        $vaccinForm->handleRequest($request);

        if ($vaccinForm->isSubmitted() && $vaccinForm->isValid()) {
            $em->persist($vaccin);
            $em->flush();
            $this->addFlash('success', 'Vaccin ajouté avec succès.');

            return $this->redirectToRoute('app_mouton_show', ['id' => $mouton->getId()]);
        }

        $infos = (new Infos())->setMouton($mouton);
        $infosForm = $this->createForm(InfosType::class, $infos, [
            'action' => $this->generateUrl('app_mouton_show', ['id' => $mouton->getId()]),
        ]);
        $infosForm->handleRequest($request);

        if ($infosForm->isSubmitted() && $infosForm->isValid()) {
            $em->persist($infos);
            $em->flush();
            $this->addFlash('success', 'Information ajoutée avec succès.');

            return $this->redirectToRoute('app_mouton_show', ['id' => $mouton->getId()]);
        }

        return $this->render('mouton/show.html.twig', [
            'mouton' => $mouton,
            'vaccinForm' => $vaccinForm,
            'infosForm' => $infosForm,
        ]);
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
