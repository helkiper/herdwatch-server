<?php

namespace App\Service;

use App\Filter\FilterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var iterable
     */
    private iterable $filters;

    /**
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @param iterable $filters
     */
    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        iterable $filters
    ) {
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->filters = $filters;
    }

    /**
     * @param string $entityClass
     * @param string $serializationGroup
     * @param array $searchParams
     * @return JsonResponse
     */
    public function list(string $entityClass, string $serializationGroup, array $searchParams = []): JsonResponse
    {
        $queryBuilder = $this->entityManager->getRepository($entityClass)->createQueryBuilder('e');
        /** @var FilterInterface $filter */
        foreach ($this->filters as $filter) {
            if ($filter->support($queryBuilder, $searchParams)) {
                $filter->processQueryBuilder($queryBuilder, $searchParams);
            }
        }
        $entities = $queryBuilder->getQuery()->getResult();

        return new JsonResponse(
            $this->serializer->serialize($entities, 'json', ['groups' => $serializationGroup]),
            Response::HTTP_OK,
            [],
            true
        );
    }

    /**
     * @param string $entityClass
     * @param string $serializationGroup
     * @param int $id
     * @return JsonResponse
     */
    public function get(string $entityClass, string $serializationGroup, int $id): JsonResponse
    {
        $entity = $this->entityManager->getRepository($entityClass)->find($id);
        if (!$entity instanceof $entityClass) {
            return new JsonResponse(
                ['message' => 'entity not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        return new JsonResponse(
            $this->serializer->serialize($entity, 'json', ['groups' => $serializationGroup]),
            Response::HTTP_OK,
            [],
            true
        );
    }

    /**
     * @param string $entityClass
     * @param string $serializationGroup
     * @param Request $request
     * @return JsonResponse
     */
    public function create(
        string $entityClass,
        string $serializationGroup,
        Request $request
    ): JsonResponse {
        $entity = $this->serializer->deserialize(
            $request->getContent(),
            $entityClass,
            'json',
            ['groups' => $serializationGroup . '.create']
        );

        $errors = $this->validator->validate($entity);
        if (count($errors) > 0) {
            return new JsonResponse(
                ['message' => $this->joinViolationMessages($errors)],
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return new JsonResponse(
            $this->serializer->serialize($entity, 'json', ['groups' => $serializationGroup]),
            Response::HTTP_CREATED,
            [],
            true
        );
    }

    /**
     * @param string $entityClass
     * @param string $serializationGroup
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(
        string $entityClass,
        string $serializationGroup,
        Request $request,
        int $id
    ): JsonResponse {
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
                AbstractNormalizer::OBJECT_TO_POPULATE => $entity,
            ]
        );

        $errors = $this->validator->validate($entity);
        if (count($errors) > 0) {
            return new JsonResponse(
                ['message' => $this->joinViolationMessages($errors)],
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->entityManager->flush();

        return new JsonResponse(
            $this->serializer->serialize($entity, 'json', ['groups' => $serializationGroup]),
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

    private function joinViolationMessages(ConstraintViolationListInterface $errors): string
    {
        $errorMessages = [];

        /** @var ConstraintViolationInterface $error */
        foreach ($errors as $error) {
            $errorMessages[] = $error->getMessage();
        }

        return implode(';' . PHP_EOL, $errorMessages);
    }
}
