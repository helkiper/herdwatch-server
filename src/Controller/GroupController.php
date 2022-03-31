<?php

namespace App\Controller;

use App\Entity\Group;
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

class GroupController extends AbstractController
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
     * @Route("/api/group", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="List of groups",
     *     @OA\JsonContent(
     *          type="array",
     *          @OA\Items(ref=@Model(type=Group::class, groups={"default"}))
     *     )
     * )
     * @OA\Tag(name="Groups")
     *
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        return $this->handler->list(Group::class);
    }

    /**
     * @Route("/api/group/{id}", methods={"GET"})
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of group",
     *     required=true,
     *     @OA\Schema(type="integer")
     * )
     * @OA\Response(
     *     response=200,
     *     description="group",
     *     @OA\JsonContent(ref=@Model(type=Group::class, groups={"default"}))
     * )
     * @OA\Response(
     *     response=404,
     *     description="if group not found",
     *     @OA\JsonContent(
     *          @OA\Property(type="string", property="message", example={"message": "Group not found"})
     *     )
     * )
     * @OA\Tag(name="Groups")
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getItem(int $id): JsonResponse
    {
        return $this->handler->get(Group::class, $id);
    }

    /**
     * @Route("/api/group", methods={"POST"})
     * @OA\RequestBody(
     *     description="group to create",
     *     @OA\JsonContent(
     *          ref=@Model(type=Group::class, groups={"group.write"}),
     *          example={
     *              "name": "group 1"
     *          }
     *     ),
     *     required=true
     * )
     * @OA\Response(
     *     response=201,
     *     description="Created group",
     *     @OA\JsonContent(
     *          ref=@Model(type=Group::class, groups={"default"})
     *     )
     * )
     * @OA\Tag(name="Groups")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        return $this->handler->create(Group::class, 'group.write', $request);
    }

    /**
     * @Route("/api/group/{id}", methods={"PUT"})
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of group",
     *     required=true,
     *     @OA\Schema(type="integer")
     * )
     * @OA\RequestBody(
     *     description="group to update data",
     *     @OA\JsonContent(
     *          ref=@Model(type=Group::class, groups={"group.write"}),
     *          example={
     *              "name": "group 1"
     *          }
     *     ),
     *     required=true
     * )
     * @OA\Response(
     *     response=200,
     *     description="group",
     *     @OA\JsonContent(ref=@Model(type=Group::class, groups={"default"}))
     * )
     * @OA\Response(
     *     response=404,
     *     description="if group not found",
     *     @OA\JsonContent(
     *          @OA\Property(type="string", property="message", example={"message": "Group not found"})
     *     )
     * )
     * @OA\Tag(name="Groups")
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        return $this->handler->update(Group::class, $request, $id);
    }

    /**
     * @Route("/api/group/{id}", methods={"DELETE"})
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID of group",
     *     required=true,
     *     @OA\Schema(type="integer")
     * )
     * @OA\Response(
     *     response=204,
     *     description="Group seccessfuly deleted"
     * )
     * @OA\Response(
     *     response=404,
     *     description="if group not found",
     *     @OA\JsonContent(
     *          @OA\Property(type="string", property="message", example={"message": "Group not found"})
     *     )
     * )
     * @OA\Tag(name="Groups")
     *
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        return $this->handler->delete(Group::class, $id);
    }
}
