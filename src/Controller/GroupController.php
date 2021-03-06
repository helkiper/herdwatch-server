<?php

namespace App\Controller;

use App\Entity\Group;
use App\Service\ApiResourceCrudHandler;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AbstractController
{
    /**
     * @var ApiResourceCrudHandler
     */
    private $handler;

    /**
     * @param ApiResourceCrudHandler $handler
     */
    public function __construct(ApiResourceCrudHandler $handler)
    {
        $this->handler = $handler;
    }

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
        return $this->handler->list(Group::class, 'group');
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
        return $this->handler->get(Group::class, 'group', $id);
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
     * @OA\Response(
     *     response=400,
     *     description="Validation error",
     *     @OA\JsonContent(
     *          @OA\Property(type="string", property="message", example={"message": "some constraint violation"})
     *     )
     * )
     * @OA\Tag(name="Groups")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        return $this->handler->create(Group::class, 'group', $request);
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
     *          ref=@Model(type=Group::class, groups={"group"}),
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
     *     response=400,
     *     description="Validation error",
     *     @OA\JsonContent(
     *          @OA\Property(type="string", property="message", example={"message": "some constraint violation"})
     *     )
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
        return $this->handler->update(Group::class, 'group', $request, $id);
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
