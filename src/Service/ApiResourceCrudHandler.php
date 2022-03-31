<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ApiResourceCrudHandler
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
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $entityClass
     * @return JsonResponse
     */
    public function list(string $entityClass): JsonResponse
    {
        $entities = $this->entityManager->getRepository($entityClass)->findAll();

        return new JsonResponse(
            $this->serializer->serialize($entities, 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }

    /**
     * @param string $entityClass
     * @param int $id
     * @return JsonResponse
     */
    public function get(string $entityClass, int $id): JsonResponse
    {
        $entity = $this->entityManager->getRepository($entityClass)->find($id);
        if (!$entity instanceof $entityClass) {
            return new JsonResponse(
                ['message' => 'entity not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        return new JsonResponse(
            $this->serializer->serialize($entity, 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }

    /**
     * @param string $entityClass
     * @param string $deserializationGroup
     * @param Request $request
     * @return JsonResponse
     */
    public function create(string $entityClass, string $deserializationGroup, Request $request): JsonResponse
    {
        $entity = $this->serializer->deserialize(
            $request->getContent(),
            $entityClass,
            'json',
            ['groups' => $deserializationGroup]
        );

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return new JsonResponse(
            $this->serializer->serialize($entity, 'json'),
            Response::HTTP_CREATED,
            [],
            true
        );
    }

    /**
     * @param string $entityClass
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(string $entityClass, Request $request, int $id): JsonResponse
    {
        $entity = $this->entityManager->getRepository($entityClass)->find($id);
        if (!$entity instanceof $entityClass) {
            return new JsonResponse(
                ['message' => 'entity not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $this->serializer->deserialize(
            $request->getContent(),
            $entityClass,
            'json',
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $entity
            ]
        );

        $this->entityManager->flush();

        return new JsonResponse(
            $this->serializer->serialize($entity, 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }

    /**
     * @param string $entityClass
     * @param int $id
     * @return Response
     */
    public function delete(string $entityClass, int $id): Response
    {
        $entity = $this->entityManager->getRepository($entityClass)->find($id);
        if (!$entity instanceof $entityClass) {
            return new JsonResponse(
                ['message' => 'entity not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
