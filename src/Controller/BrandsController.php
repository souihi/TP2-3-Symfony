<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Form\BrandType;
use App\Repository\BrandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/brands')]
class BrandsController extends AbstractController
{
    #[Route('/', name: 'app_brands_index', methods: ['GET'])]
    public function index(BrandRepository $brandRepository): Response
    {
        return $this->render('brands/index.html.twig', [
            'brands' => $brandRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_brands_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $brand = new Brand();
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($brand);
            $entityManager->flush();

            return $this->redirectToRoute('app_brands_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('brands/new.html.twig', [
            'brand' => $brand,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_brands_show', methods: ['GET'])]
    public function show(Brand $brand): Response
    {
        return $this->render('brands/show.html.twig', [
            'brand' => $brand,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_brands_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Brand $brand, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_brands_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('brands/edit.html.twig', [
            'brand' => $brand,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_brands_delete', methods: ['POST'])]
    public function delete(Request $request, Brand $brand, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$brand->getId(), $request->request->get('_token'))) {
            $entityManager->remove($brand);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_brands_index', [], Response::HTTP_SEE_OTHER);
    }
}
