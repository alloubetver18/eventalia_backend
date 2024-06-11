<?php

namespace App\Controller;

use App\Entity\Generos;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/types')]
class ThemeController extends AbstractController
{
    #[Route(path: '/list', name: 'type_list', methods: ['GET'])]
    public function TypeList(EntityManagerInterface $entityManager): JsonResponse
    {
        $generos = $entityManager->getRepository(Generos::class)->findAll();
        $json = new JsonResponse();
        $generosArray = [];
        if (empty($generos)) {
            $json->setData(['result' => 'failure', 'message' => 'No hay generos', 'data' => []]);
        } else {
            foreach ($generos as $genero) {
                $generosArray[] = [
                    'nombre' => $genero->getDenominacion(),
                    // Agrega más campos según sea necesario
                ];
            }
            $json->setData(['result' => 'success', 'data' => $generosArray]);
        }

        return $json;
    }
}
