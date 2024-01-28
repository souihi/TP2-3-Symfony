<?php

namespace App\Controller;

use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Security;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Entity\Material;
use App\Repository\MaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
class MaterialController extends AbstractController
{
    #[Route('/api/materials', name: 'material', methods:['GET'])]
       /**         
        *  @OA\Response(
        *     response=200,
        *     description="Retourne touts les matériaux dispo",
        *     @OA\JsonContent(
        *        type="array",
        *        @OA\Items(ref=@Model(type=Material::class, groups={"pen_list"}))
        *     )
        * )
        * @OA\Tag(name="Material")
        * @IsGranted("ROLE_ADMIN")
        * @Security(name="Bearer")
        */
    public function getMaterial(MaterialRepository $materialRepository, SerializerInterface $serializer  ): JsonResponse
    {

        $materialList = $materialRepository->findAll();
        $jsonMaterialList = $serializer->serialize($materialList, 'json', ['groups' => 'pen_list']);
        return new JsonResponse($jsonMaterialList, Response::HTTP_OK, [], true);
    }
    
    #[Route('/api/material/{id}', name: 'detailMaterial', methods:['GET'])]
           /**         
        *  @OA\Response(
        *     response=200,
        *     description="Retourne un materiel par l'identifiant",
        *     @OA\JsonContent(
        *        type="array",
        *        @OA\Items(ref=@Model(type=Material::class, groups={"pen_list"}))
        *     )
        * )
        * @OA\Tag(name="Material")
        * @IsGranted("ROLE_ADMIN")
        * @Security(name="Bearer")
        */
    public function getDetailMaterial(int $id,MaterialRepository $materialRepository, SerializerInterface $serializer  ): JsonResponse
    {
        $material = $materialRepository->find($id);
        if ($material) {
            $jsonMaterial = $serializer->serialize($material, 'json', ['groups' => 'pen_list']);
            return new JsonResponse($jsonMaterial, Response::HTTP_OK, [], true);
        }else {
            return new JsonResponse(Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/api/material/{id}', name: 'deleteMaterial', methods:['DELETE'])]
    /**         
        *  @OA\Response(
        *     response=200,
        *     description="Supprime un materiel grâce à l'identifiant",
        *     @OA\JsonContent(
        *        type="array",
        *        @OA\Items(ref=@Model(type=Material::class, groups={"pen_list"}))
        *     )
        * )
        * @OA\Tag(name="Material")
        * @IsGranted("ROLE_ADMIN")
        * @Security(name="Bearer")
        */
    public function getDeleteMaterial(int $id,MaterialRepository $materialRepository, EntityManagerInterface $em): JsonResponse
    {
        $material = $materialRepository->find($id);
        if ($material) {
            $em ->remove($material);
            $em -> flush();
            return new JsonResponse($em, Response::HTTP_OK, [], true);
        }else {
            return new JsonResponse($em, Response::HTTP_NOT_FOUND);
        }
    }

    
    #[Route('/api/material', name: 'postMaterial', methods:['POST'])]
        /**         
        *  @OA\Response(
        *     response=200,
        *     description="Ajout d'un matériel",
        *     @OA\JsonContent(
        *        type="array",
        *        @OA\Items(ref=@Model(type=Material::class, groups={"pen_list"}))
        *     )
        * )
        * @OA\Tag(name="Material")
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
            $material = new Material();
            $material->setName($data['name']);

            $em->persist($material);
            $em->flush();

            return $this->json($material, context: [
                'groups' => ['pen_list'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/api/material/{id}', name: 'updateMaterial', methods: ['PUT','PATCH'])]
     /**         
        *  @OA\Response(
        *     response=200,
        *     description="	Met à jour un matériel",
        *     @OA\JsonContent(
        *        type="array",
        *        @OA\Items(ref=@Model(type=Material::class, groups={"pen_list"}))
        *     )
        * )
        * @OA\Tag(name="Material")
        * @IsGranted("ROLE_ADMIN")
        * @Security(name="Bearer")
        */
    public function update(
        Material $material,
        Request $request,
        EntityManagerInterface $em,

    ): JsonResponse {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour créer un nouveau Stylo
            $material->setName($data['name']);

            $em->persist($material);
            $em->flush();

            return $this->json($material, context: [
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
