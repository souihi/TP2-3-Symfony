<?php

namespace App\Controller;
use Faker\Factory;
use App\Entity\Pen;
use OpenApi\Annotations as OA;
use App\Repository\PenRepository;
use App\Repository\TypeRepository;
use App\Repository\BrandRepository;
use App\Repository\ColorRepository;
use App\Repository\MaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Expression;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PenController extends AbstractController
{
    #[Route('/api/pens', name: 'pen', methods:['GET'])]
    /**         
        *  @OA\Response(
        *     response=200,
        *     description="Retourne touts les stylos dispo",
        *     @OA\JsonContent(
        *        type="array",
        *        @OA\Items(ref=@Model(type=Pen::class, groups={"pen_list"}))
        *     )
        * )
        * @OA\Tag(name="Pen")
        * @IsGranted("ROLE_ADMIN")
        * @Security(name="Bearer")
        */
    public function getPen(PenRepository $penRepository, SerializerInterface $serializer  ): JsonResponse
    {

        $penList = $penRepository->findAll();
        $jsonPenList = $serializer->serialize($penList, 'json', ['groups' => 'pen_list']);
        return new JsonResponse($jsonPenList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/pen/{id}', name: 'detailPen', methods:['GET'])]
     /**         
        *  @OA\Response(
        *     response=200,
        *     description="Retourne un stylos grâce à l'id",
        *     @OA\JsonContent(
        *        type="array",
        *        @OA\Items(ref=@Model(type=Pen::class, groups={"pen_list"}))
        *     )
        * )
        * @OA\Tag(name="Pen")
        * @IsGranted("ROLE_USER")
        * @Security(name="Bearer")
        */

    public function getDetailPen(int $id,PenRepository $penRepository, SerializerInterface $serializer  ): JsonResponse
    {
        $pen = $penRepository->find($id);
        if ($pen) {
            $jsonPen = $serializer->serialize($pen, 'json', ['groups' => 'pen_list']);
            return new JsonResponse($jsonPen, Response::HTTP_OK, [], true);
        }else {
            return new JsonResponse( Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/api/pen/{id}', name: 'deletePen', methods:['DELETE'])]
    /**         
        *  @OA\Response(
        *     response=200,
        *     description="Supprime un stylo",
        *     @OA\JsonContent(
        *        type="array",
        *        @OA\Items(ref=@Model(type=Pen::class, groups={"pen_list"}))
        *     )
        * )
        * @OA\Tag(name="Pen")
        * @IsGranted("ROLE_ADMIN")
        * @Security(name="Bearer")
        */

    public function getDeletePen(int $id,PenRepository $penRepository, EntityManagerInterface $em): JsonResponse
    {
        $pen = $penRepository->find($id);
        if ($pen) {
            $em ->remove($pen);
            $em -> flush();
            return new JsonResponse($em, Response::HTTP_OK, [], true);
        }else {
            return new JsonResponse($em, Response::HTTP_NOT_FOUND);
        }
    }

    
    #[Route('/api/pen', name: 'postePen', methods:['POST'])]
        /**         
        *  @OA\Response(
        *     response=200,
        *     description="Ajout d'un stylos",
        *     @OA\JsonContent(
        *        type="array",
        *        @OA\Items(ref=@Model(type=Pen::class, groups={"pen_list"}))
        *     )
        * )
        * @OA\Tag(name="Pen")
        * @IsGranted("ROLE_ADMIN")
        * @Security(name="Bearer")
        */

    public function add(
        Request $request,
        EntityManagerInterface $em,
        TypeRepository $typeRepository,
        MaterialRepository $materialRepository,
        BrandRepository $brandRepository,
        ColorRepository $colorRepository
    ): Response {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            $faker = Factory::create();

            // On traite les données pour créer un nouveau Stylo
            $pen = new Pen();
            $pen->setName($data['name']);
            $pen->setPrice($data['price']);
            $pen->setDescription($data['description']);
            $pen->setRef($faker->unique()->ean13);

            // Récupération du type de stylo
            if(!empty($data['type']))
            {
                $type = $typeRepository->find($data['type']);

                if(!$type)
                    throw new \Exception("Le type renseigné n'existe pas");

                $pen->setType($type);
            }

            // Récupération du matériel
            if(!empty($data['material']))
            {
                $material = $materialRepository->find($data['material']);

                if(!$material)
                    throw new \Exception("Le matériel renseigné n'existe pas");

                $pen->setMaterial($material);
            }
            // Récupération du brand
            if(!empty($data['brand']))
            {
                $brand = $brandRepository->find($data['brand']);

                if(!$brand)
                    throw new \Exception("La marque renseigné n'existe pas");

                $pen->setBrand($brand);
            }
            // Récupération de color
            if(!empty($data['color']))
            {
                $color = $colorRepository->find($data['color']);

                if(!$color)
                    throw new \Exception("La couleur renseigné n'existe pas");

                $pen->addColor($color);
            }

            $em->persist($pen);
            $em->flush();

            return $this->json($pen, context: [
                'groups' => ['pen_list'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/api/pen/{id}', name: 'updatePen', methods: ['PUT','PATCH'])]
        /**         
        *  @OA\Response(
        *     response=200,
        *     description="Met à jour un stylo",
        *     @OA\JsonContent(
        *        type="array",
        *        @OA\Items(ref=@Model(type=Pen::class, groups={"pen_list"}))
        *     )
        * )
        * @OA\Tag(name="Pen")
        * @IsGranted("ROLE_ADMIN")
        * @Security(name="Bearer")
        */

    public function update(
        Pen $pen,
        Request $request,
        EntityManagerInterface $em,
        TypeRepository $typeRepository,
        MaterialRepository $materialRepository
    ): JsonResponse {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour créer un nouveau Stylo
            $pen->setName($data['name']);
            $pen->setPrice($data['price']);
            $pen->setDescription($data['description']);

            // Récupération du type de stylo
            if(!empty($data['type']))
            {
                $type = $typeRepository->find($data['type']);

                if(!$type)
                    throw new \Exception("Le type renseigné n'existe pas");

                $pen->setType($type);
            }

            // Récupération du matériel
            if(!empty($data['material']))
            {
                $material = $materialRepository->find($data['material']);

                if(!$material)
                    throw new \Exception("Le matériel renseigné n'existe pas");

                $pen->setMaterial($material);
            }

            $em->persist($pen);
            $em->flush();

            return $this->json($pen, context: [
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

