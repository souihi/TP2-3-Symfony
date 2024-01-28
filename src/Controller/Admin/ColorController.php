<?php

namespace App\Controller;
use App\Entity\Color;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Security;
use Nelmio\ApiDocBundle\Annotation\Model;
// use Symfony\Component\Security\Core\Annotation\Security;
use App\Repository\ColorRepository;
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
class ColorController extends AbstractController
{
    #[Route('/api/colors', name: 'color', methods:['GET'])]
        /**         
        *  @OA\Response(
        *     response=200,
        *     description="Retourne toutes les couleurs dispo",
        *     @OA\JsonContent(
        *        type="array",
        *        @OA\Items(ref=@Model(type=Color::class, groups={"pen_list"}))
        *     )
        * )
        * @OA\Tag(name="Color")
        * @IsGranted("ROLE_ADMIN")
        * @Security(name="Bearer")
        */
    public function getColor(ColorRepository $colorRepository, SerializerInterface $serializer  ): JsonResponse
    {

        $colorList = $colorRepository->findAll();
        $jsonColorList = $serializer->serialize($colorList, 'json', ['groups' => 'pen_list']);
        return new JsonResponse($jsonColorList, Response::HTTP_OK, [], true);
    }
    #[Route('/api/color/{id}', name: 'detailColor', methods:['GET'])]
        /**         
        *  @OA\Response(
        *     response=200,
        *     description="Retourne la couleur par l'id",
        *     @OA\JsonContent(
        *        type="array",
        *        @OA\Items(ref=@Model(type=Color::class, groups={"pen_list"}))
        *     )
        * )
        * @OA\Tag(name="Color")
        * @IsGranted("ROLE_ADMIN")
        * @Security(name="Bearer")
        */
    public function getDetailColor(int $id,ColorRepository $colorRepository, SerializerInterface $serializer  ): JsonResponse
    {
        $color = $colorRepository->find($id);
        if ($color) {
            $jsonColor = $serializer->serialize($color, 'json', ['groups' => 'pen_list']);
            return new JsonResponse($jsonColor, Response::HTTP_OK, [], true);
        }else {
            return new JsonResponse(Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/api/color/{id}', name: 'deleteColor', methods:['DELETE'])]
        /**         
        *  @OA\Response(
        *     response=200,
        *     description="Supprime la couleur grâce à l'identifiant",
        *     @OA\JsonContent(
        *        type="array",
        *        @OA\Items(ref=@Model(type=Color::class, groups={"pen_list"}))
        *     )
        * )
        * @OA\Tag(name="Color")
        * @IsGranted("ROLE_ADMIN")
        * @Security(name="Bearer")
        */
    public function getDeleteColor(int $id,ColorRepository $colorRepository, EntityManagerInterface $em): JsonResponse
    {
        $color = $colorRepository->find($id);
        if ($color) {
            $em ->remove($color);
            $em -> flush();
            return new JsonResponse($em, Response::HTTP_OK, [], true);
        }else {
            return new JsonResponse($em, Response::HTTP_NOT_FOUND);
        }
    }

    
    #[Route('/api/color', name: 'postColor', methods:['POST'])]
        /**         
        *  @OA\Response(
        *     response=200,
        *     description="Ajout d'une couleur",
        *     @OA\JsonContent(
        *        type="array",
        *        @OA\Items(ref=@Model(type=Color::class, groups={"pen_list"}))
        *     )
        * )
        * @OA\Tag(name="Color")
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
            $color = new Color();
            $color->setName($data['name']);

            $em->persist($color);
            $em->flush();

            return $this->json($color, context: [
                'groups' => ['pen_list'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/api/color/{id}', name: 'updateColor', methods: ['PUT','PATCH'])]
        /**         
        *  @OA\Response(
        *     response=200,
        *     description="Met à jour la couleur",
        *     @OA\JsonContent(
        *        type="array",
        *        @OA\Items(ref=@Model(type=Color::class, groups={"pen_list"}))
        *     )
        * )
        * @OA\Tag(name="Color")
        * @IsGranted("ROLE_ADMIN")
        * @Security(name="Bearer")
        */
    public function update(
        Color $color,
        Request $request,
        EntityManagerInterface $em,

    ): JsonResponse {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour créer un nouveau Stylo
            $color->setName($data['name']);

            $em->persist($color);
            $em->flush();

            return $this->json($color, context: [
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
