<?php

namespace App\Controller;

use App\Entity\DevMetalGroup;
use App\Repository\DevMetalGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[Route('/devmetal')]
final class DevMetalGroupController extends AbstractController
{
    #[Route('', name: 'app_dev_metal_group',methods: ['GET'])]
    public function index(
        DevMetalGroupRepository $devMetalGroupRepository,
        SerializerInterface $serializer,
        TagAwareCacheInterface $cache
    ): JsonResponse
    {

        $idCache = "devMetalGroup.findAll";
        $cachedResources = $cache->get($idCache, function (ItemInterface $item) use ($devMetalGroupRepository, $serializer) {
            $item->tag("devMetalGroup");
            $item->tag("devMetalSong");
            $data = $devMetalGroupRepository->findAll();
            $jsonData = $serializer->serialize($data, 'json', ["groups" => [ "devMetalGroup"]]);
            return $jsonData;
        });

//
//
//        $jsonData = $serializer->serialize($data, 'json', ["groups" => [ "devMetalGroup"]]);

        return new JsonResponse($cachedResources, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('', name: 'app_dev_metal_group_create',methods: ['POST'])]
    public function create(TagAwareCacheInterface $cache, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator): JsonResponse
    {
        $newDevMetalGroup = $serializer->deserialize($request->getContent(), DevMetalGroup::class, 'json');
        // Est égal à
        //        $data = $request->toArray();
        //        $devMetalGroup->setName($data['name']);

        $errors = $validator->validate($newDevMetalGroup);
        if($errors->count() > 0){
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $entityManager->persist($newDevMetalGroup);
        $entityManager->flush();
        $cache->invalidateTags(["ga"]);
        $jsonData = $serializer->serialize($newDevMetalGroup, 'json', ["groups" => [ "devMetalGroup"]]);
        $location = $urlGenerator->generate('app_dev_metal_group_get', ['id' => $newDevMetalGroup->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonData, JsonResponse::HTTP_CREATED, ["Location" => $location], true);
    }


    #[Route('/{id}', name: 'app_dev_metal_group_get',methods: ['GET'])]
    public function getById(DevMetalGroup $devMetalGroup ,Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $jsonData = $serializer->serialize($devMetalGroup, 'json', ["groups" => [ "devMetalGroup"]]);
        return new JsonResponse($jsonData, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'app_dev_metal_group_update',methods: ['PUT', 'PATCH'])]
    public function update(DevMetalGroup $devMetalGroup ,Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $serializer->deserialize($request->getContent(), DevMetalGroup::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $devMetalGroup]);
        // Est égal à
        //        $data = $request->toArray();
        //        $devMetalGroup->setName($data['name'] ?? $devMetalGroup->getName());

        $entityManager->persist($devMetalGroup);
        $entityManager->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/{id}', name: 'app_dev_metal_group_delete',methods: ['DELETE'])]
    public function delete(DevMetalGroup $devMetalGroup ,Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $entityManager->remove($devMetalGroup);
        $entityManager->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

}
