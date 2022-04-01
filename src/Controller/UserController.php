<?php

namespace App\Controller;

use App\Entity\Group;
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

    /**
     * @Route("/api/user", methods={"GET"})
     * @OA\Parameter(
     *     name="groupId",
     *     in="query",
     *     description="ID of group",
     *     required=false,
     *     @OA\Schema(type="integer")
     * )
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
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $searchParams = $request->query->has('groupId')
            ? ['groupId' => $request->query->get('groupId')]
            : [];

        return $this->handler->list(User::class, 'user', $searchParams);
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
        return $this->handler->get(User::class, 'user', $id);
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
     * @OA\Response(
     *     response=400,
     *     description="Validation error",
     *     @OA\JsonContent(
     *          @OA\Property(type="string", property="message", example={"message": "some constraint violation"})
     *     )
     * )
     * @OA\Tag(name="Users")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        return $this->handler->create(User::class, 'user', $request);
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
     *     response=400,
     *     description="Validation error",
     *     @OA\JsonContent(
     *          @OA\Property(type="string", property="message", example={"message": "some constraint violation"})
     *     )
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
        return $this->handler->update(User::class, 'user', $request, $id);
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

    /**
     * @Route("/api/user/attach/{userId}/{groupId}", methods={"PATCH"})
     * @OA\Parameter(
     *     name="userId",
     *     in="path",
     *     description="ID of user",
     *     required=true,
     *     @OA\Schema(type="integer")
     * )
     * @OA\Parameter(
     *     name="groupId",
     *     in="path",
     *     description="ID of group",
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
     *     description="if user or group not found",
     *     @OA\JsonContent(
     *          @OA\Property(type="string", property="message", example={"message": "User not found"})
     *     )
     * )
     * @OA\Tag(name="Users")
     *
     * @param int $userId
     * @param int $groupId
     * @return JsonResponse
     */
    public function attach(int $userId, int $groupId): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        if (!$user instanceof User) {
            return new JsonResponse(
                ['message' => 'user not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $group = $this->entityManager->getRepository(Group::class)->find($groupId);
        if (!$group instanceof Group) {
            return new JsonResponse(
                ['message' => 'group not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $group->addUser($user);
        $this->entityManager->flush();

        return new JsonResponse(
            $this->serializer->serialize($user, 'json', ['groups' => 'user']),
            Response::HTTP_OK,
            [],
            true
        );
    }

    /**
     * @Route("/api/user/detach/{userId}/{groupId}", methods={"PATCH"})
     * @OA\Parameter(
     *     name="userId",
     *     in="path",
     *     description="ID of user",
     *     required=true,
     *     @OA\Schema(type="integer")
     * )
     * @OA\Parameter(
     *     name="groupId",
     *     in="path",
     *     description="ID of group",
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
     *     description="if user or group not found",
     *     @OA\JsonContent(
     *          @OA\Property(type="string", property="message", example={"message": "User not found"})
     *     )
     * )
     * @OA\Tag(name="Users")
     *
     * @param int $userId
     * @param int $groupId
     * @return JsonResponse
     */
    public function detach(int $userId, int $groupId): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        if (!$user instanceof User) {
            return new JsonResponse(
                ['message' => 'user not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $group = $this->entityManager->getRepository(Group::class)->find($groupId);
        if (!$group instanceof Group) {
            return new JsonResponse(
                ['message' => 'group not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $group->removeUser($user);
        $this->entityManager->flush();

        return new JsonResponse(
            $this->serializer->serialize($user, 'json', ['groups' => 'user']),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
