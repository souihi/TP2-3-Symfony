<?php

namespace App\Controller;
use App\Entity\Type;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Security;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Repository\TypeRepository;
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

class TypeController extends AbstractController
{
    #[Route('/api/types', name: 'type', methods:['GET'])]
    /**         
        *  @OA\Response(
        *     response=200,
        *     description="Retourne touts les types dispo",
        *     @OA\JsonContent(
        *        type="array",
        *        @OA\Items(ref=@Model(type=Type::class, groups={"pen_list"}))
        *     )
        * )
        * @OA\Tag(name="Type")
        * @IsGranted("ROLE_ADMIN")
        * @Security(name="Bearer")
        */
    public function getType(TypeRepository $typeRepository, SerializerInterface $serializer  ): JsonResponse
    {

        $typeList = $typeRepository->findAll();
        $jsonTypeList = $serializer->serialize($typeList, 'json', ['groups' => 'pen_list']);
        return new JsonResponse($jsonTypeList, Response::HTTP_OK, [], true);
    }
    #[Route('/api/type/{id}', name: 'detailType', methods:['GET'])]
     /**         
        *  @OA\Response(
        *     response=200,
        *     description="Retourne les type par l'id",
        *     @OA\JsonContent(
        *        type="array",
        *        @OA\Items(ref=@Model(type=Type::class, groups={"pen_list"}))
        *     )
        * )
        * @OA\Tag(name="Type")
        * @IsGranted("ROLE_ADMIN")
        * @Security(name="Bearer")
        */
    public function getDetailType(int $id,TypeRepository $typeRepository, SerializerInterface $serializer  ): JsonResponse
    {
        $type = $typeRepository->find($id);
        if ($type) {
            $jsonType = $serializer->serialize($type, 'json', ['groups' => 'pen_list']);
            return new JsonResponse($jsonType, Response::HTTP_OK, [], true);
        }else {
            return new JsonResponse(Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/api/type/{id}', name: 'deleteType', methods:['DELETE'])]
      /**         
        *  @OA\Response(
        *     response=200,
        *     description="Supprime le type grâce à l'identifiant",
        *     @OA\JsonContent(
        *        type="array",
        *        @OA\Items(ref=@Model(type=Type::class, groups={"pen_list"}))
        *     )
        * )
        * @OA\Tag(name="Type")
        * @IsGranted("ROLE_ADMIN")
        * @Security(name="Bearer")
        */
    public function getDeleteType(int $id,TypeRepository $typeRepository, EntityManagerInterface $em): JsonResponse
    {
        $type = $typeRepository->find($id);
        if ($type) {
            $em ->remove($type);
            $em -> flush();
            return new JsonResponse($em, Response::HTTP_OK, [], true);
        }else {
            return new JsonResponse($em, Response::HTTP_NOT_FOUND);
        }
    }

    
    #[Route('/api/type', name: 'postType', methods:['POST'])]
        /**         
        *  @OA\Response(
        *     response=200,
        *     description="Ajout d'un type",
        *     @OA\JsonContent(
        *        type="array",
        *        @OA\Items(ref=@Model(type=Type::class, groups={"pen_list"}))
        *     )
        * )
        * @OA\Tag(name="Type")
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
            $type = new Type();
            $type->setName($data['name']);

            $em->persist($type);
            $em->flush();

            return $this->json($type, context: [
                'groups' => ['pen_list'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/api/type/{id}', name: 'updateType', methods: ['PUT','PATCH'])]
     /**         
        *  @OA\Response(
        *     response=200,
        *     description="Met à jour le type",
        *     @OA\JsonContent(
        *        type="array",
        *        @OA\Items(ref=@Model(type=Type::class, groups={"pen_list"}))
        *     )
        * )
        * @OA\Tag(name="Type")
        * @IsGranted("ROLE_ADMIN")
        * @Security(name="Bearer")
        */
    public function update(
        Type $type,
        Request $request,
        EntityManagerInterface $em,

    ): JsonResponse {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour créer un nouveau Stylo
            $type->setName($data['name']);

            $em->persist($type);
            $em->flush();

            return $this->json($type, context: [
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
