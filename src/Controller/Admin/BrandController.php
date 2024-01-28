<?php

namespace App\Controller;

use App\Entity\Brand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BrandRepository;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Security;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Annotation\Groups;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
class BrandController extends AbstractController
{
    #[Route('/api/brands', name: 'brand', methods:['GET'])]
            /**         
        *  @OA\Response(
        *     response=200,
        *     description="Retourne toutes les marques dispo",
        *     @OA\JsonContent(
        *        type="array",
        *        @OA\Items(ref=@Model(type=Brand::class, groups={"pen_list"}))
        *     )
        * )
        * @OA\Tag(name="Brand")
        * @IsGranted("ROLE_ADMIN")
        * @Security(name="Bearer")
        */
    public function getBrand(BrandRepository $brandRepository, SerializerInterface $serializer  ): JsonResponse
    {

        $brandList = $brandRepository->findAll();
        $jsonBrandList = $serializer->serialize($brandList, 'json', ['groups' => 'pen_list']);
        return new JsonResponse($jsonBrandList, Response::HTTP_OK, [], true);
    }
    #[Route('/api/brands/{id}', name: 'detailBrand', methods:['GET'])]
            /**         
        *  @OA\Response(
        *     response=200,
        *     description="Retourne la marque par l'id",
        *     @OA\JsonContent(
        *        type="array",
        *        @OA\Items(ref=@Model(type=Brand::class, groups={"pen_list"}))
        *     )
        * )
        * @OA\Tag(name="Brand")
        * @Security(name="Bearer")
        */
    public function getDetailBrand(int $id,BrandRepository $brandRepository, SerializerInterface $serializer  ): JsonResponse
    {
        $brand = $brandRepository->find($id);
        if ($brand) {
            $jsonBrand = $serializer->serialize($brand, 'json', ['groups' => 'pen_list']);
            return new JsonResponse($jsonBrand, Response::HTTP_OK, [], true);
        }else {
            return new JsonResponse(Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/api/brands/{id}', name: 'deleteBrand', methods:['DELETE'])]
            /**         
        *  @OA\Response(
        *     response=200,
        *     description="Supprime la marque grâce à l'identifiant",
        *     @OA\JsonContent(
        *        type="array",
        *        @OA\Items(ref=@Model(type=Brand::class, groups={"pen_list"}))
        *     )
        * )
        * @OA\Tag(name="Brand")
        * @IsGranted("ROLE_ADMIN")
        * @Security(name="Bearer")
        */
    public function getDeleteBrand(int $id,BrandRepository $brandRepository, EntityManagerInterface $em): JsonResponse
    {
        $brand = $brandRepository->find($id);
        if ($brand) {
            $em ->remove($brand);
            $em -> flush();
            return new JsonResponse($em, Response::HTTP_OK, [], true);
        }else {
            return new JsonResponse($em, Response::HTTP_NOT_FOUND);
        }
    }

    
    #[Route('/api/brands', name: 'postBrand', methods:['POST'])]
            /**         
        *  @OA\Response(
        *     response=200,
        *     description="Ajout d'une marque",
        *     @OA\JsonContent(
        *        type="array",
        *        @OA\Items(ref=@Model(type=Brand::class, groups={"pen_list"}))
        *     )
        * )
        * @OA\Tag(name="Brand")
        * @IsGranted("ROLE_ADMIN")
        * @Security(name="Bearer")
        */
    public function add(
        Request $request,
        EntityManagerInterface $em,
    ): Response {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);


            // On traite les données pour créer un nouveau Stylo
            $brand = new Brand();
            $brand->setName($data['name']);

            $em->persist($brand);
            $em->flush();

            return $this->json($brand, context: [
                'groups' => ['pen_list'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/api/brand/{id}', name: 'updateBrand', methods: ['PUT','PATCH'])]
            /**         
        *  @OA\Response(
        *     response=200,
        *     description="Met à jour la marque ",
        *     @OA\JsonContent(
        *        type="array",
        *        @OA\Items(ref=@Model(type=Brand::class, groups={"pen_list"}))
        *     )
        * )
        * @OA\Tag(name="Brand")
        * @IsGranted("ROLE_ADMIN")
        * @Security(name="Bearer")
        */
    public function update(
        Brand $brand,
        Request $request,
        EntityManagerInterface $em,
    ): JsonResponse {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour créer un nouveau Stylo
            $brand->setName($data['name']);

            $em->persist($brand);
            $em->flush();

            return $this->json($brand, context: [
                'groups' => ['pen_list'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
