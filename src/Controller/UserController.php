<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\ApiResourceCrudHandler;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ApiResourceCrudHandler
     */
    private $handler;

    /**
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param ApiResourceCrudHandler $handler
     */
    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        ApiResourceCrudHandler $handler
    ) {
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->handler = $handler;
    }

    //todo response examples

    /**
     * @Route("/api/user", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="List of users",
     *     @OA\JsonContent(
     *          type="array",
     *          @OA\Items(ref=@Model(type=User::class, groups={"default"}))
     *     )
     * )
     * @OA\Tag(name="Users")
     *
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        return $this->handler->list(User::class);
    }

    /**
     * @Route("/api/user/{id}", methods={"GET"})
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of user",
     *     required=true,
     *     @OA\Schema(type="integer")
     * )
     * @OA\Response(
     *     response=200,
     *     description="user",
     *     @OA\JsonContent(ref=@Model(type=User::class, groups={"default"}))
     * )
     * @OA\Response(
     *     response=404,
     *     description="if user not found",
     *     @OA\JsonContent(
     *          @OA\Property(type="string", property="message", example={"message": "User not found"})
     *     )
     * )
     * @OA\Tag(name="Users")
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getItem(int $id): JsonResponse
    {
        return $this->handler->get(User::class, $id);
    }

    /**
     * @Route("/api/user", methods={"POST"})
     * @OA\RequestBody(
     *     description="user to create",
     *     @OA\JsonContent(
     *          ref=@Model(type=User::class, groups={"user.write"}),
     *          example={
     *              "name": "user 1",
     *              "email": "user1@mail.com"
     *          }
     *     ),
     *     required=true
     * )
     * @OA\Response(
     *     response=201,
     *     description="Created user",
     *     @OA\JsonContent(
     *          ref=@Model(type=User::class, groups={"default"})
     *     )
     * )
     * @OA\Tag(name="Users")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        return $this->handler->create(User::class, 'user.write', $request);
    }

    /**
     * @Route("/api/user/{id}", methods={"PUT"})
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of user",
     *     required=true,
     *     @OA\Schema(type="integer")
     * )
     * @OA\RequestBody(
     *     description="user to update data",
     *     @OA\JsonContent(
     *          ref=@Model(type=User::class, groups={"user.write"}),
     *          example={
     *              "name": "user 1",
     *              "email": "user1@mail.com"
     *          }
     *     ),
     *     required=true
     * )
     * @OA\Response(
     *     response=200,
     *     description="user",
     *     @OA\JsonContent(ref=@Model(type=User::class, groups={"default"}))
     * )
     * @OA\Response(
     *     response=404,
     *     description="if user not found",
     *     @OA\JsonContent(
     *          @OA\Property(type="string", property="message", example={"message": "User not found"})
     *     )
     * )
     * @OA\Tag(name="Users")
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        return $this->handler->update(User::class, $request, $id);
    }

    /**
     * @Route("/api/user/{id}", methods={"DELETE"})
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of user",
     *     required=true,
     *     @OA\Schema(type="integer")
     * )
     * @OA\Response(
     *     response=204,
     *     description="user seccussfuly deleted"
     * )
     * @OA\Response(
     *     response=404,
     *     description="if user not found",
     *     @OA\JsonContent(
     *          @OA\Property(type="string", property="message", example={"message": "User not found"})
     *     )
     * )
     * @OA\Tag(name="Users")
     *
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        return $this->handler->delete(User::class, $id);
    }
}
