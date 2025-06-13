<?php

namespace App\Controller;

use App\Entity\DevMetalSong;
use App\Repository\DevMetalGroupRepository;
use App\Repository\DevMetalSongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/song')]
final class DevMetalSongController extends AbstractController
{
    #[Route('', name: 'app_dev_metal_song',methods: ['GET'])]
    public function index(DevMetalSongRepository $devMetalSongRepository, SerializerInterface $serializer): JsonResponse
    {

        $data = $devMetalSongRepository->findAll();
        $jsonData = $serializer->serialize($data, 'json', ["groups" => "devMetalSong"]);

        return new JsonResponse($jsonData, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('', name: 'app_dev_metal_song_create',methods: ['POST'])]
    public function create(Request $request,DevMetalGroupRepository $devMetalGroupRepository, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $newDevMetalSong = $serializer->deserialize($request->getContent(), DevMetalSong::class, 'json');
        $devMetalGroupEntity = $devMetalGroupRepository->find($request->toArray()['author']);
        $newDevMetalSong->setAuthor($devMetalGroupEntity);
        // Est égal à
        //        $data = $request->toArray();
        //        $devMetalSong->setName($data['name']);
        $entityManager->persist($newDevMetalSong);
        $entityManager->flush();
        $jsonData = $serializer->serialize($newDevMetalSong, 'json', ["groups" => "devMetalSong"]);
        $location = $urlGenerator->generate('app_dev_metal_song_get', ['id' => $newDevMetalSong->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonData, JsonResponse::HTTP_CREATED, ["Location" => $location], true);
    }


    #[Route('/{id}', name: 'app_dev_metal_song_get',methods: ['GET'])]
    public function getById(DevMetalSong $devMetalSong ,Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $jsonData = $serializer->serialize($devMetalSong, 'json', ["groups" => "devMetalSong"]);
        return new JsonResponse($jsonData, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'app_dev_metal_song_update',methods: ['PUT', 'PATCH'])]
    public function update(DevMetalSong $devMetalSong ,DevMetalGroupRepository $devMetalGroupRepository, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $serializer->deserialize($request->getContent(), DevMetalSong::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $devMetalSong]);
        if(isset($request->toArray()['author'])){
            $devMetalGroupEntity = $devMetalGroupRepository->find($request->toArray()['author']);
            $devMetalSong->setAuthor($devMetalGroupEntity ?? $devMetalSong->getAuthor());
        }
        // Est égal à
        //        $data = $request->toArray();
        //        $devMetalSong->setName($data['name'] ?? $devMetalSong->getName());

        $entityManager->persist($devMetalSong);
        $entityManager->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/{id}', name: 'app_dev_metal_song_delete',methods: ['DELETE'])]
    public function delete(DevMetalSong $devMetalSong ,Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $entityManager->remove($devMetalSong);
        $entityManager->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

}
