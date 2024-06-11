<?php

namespace App\Controller;

use App\Entity\Comments;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/comments')]
class CommentController extends AbstractController
{
    /* #[Route(path: '/list', name: 'comment_list', methods: ['GET'])]
    public function commentList(EntityManagerInterface $entityManager): JsonResponse
    {
        $comentarios = $entityManager->getRepository(Comments::class)->findAll();

        //CAMBIAR TODO DESDE AQUÍ

        $json = new JsonResponse();
        $comentariosArray = [];

        if (empty($comentarios)) {
            $json->setData(['result' => 'failure', 'message' => 'No hay generos', 'data' => []]);
        } else {
            foreach ($comentarios as $comentario) {

                $comentariosArray[] = [
                    'autor' => $comentario->getUsuarios()->getNombre(),
                    'fecha' => $comentario->getFecha()->format('Y-m-d'),
                    'contenido' => $comentario->getContenido(),
                ];
            }
            $json->setData(['result' => 'success', 'data' => $comentariosArray]);
        }

        return $json;
    } */

    //Ruta para los datos de un comenario concreto
    //Ruta para añadir un comentario nuevo:
    /* 1º: Obtener el objeto de la clase Usuario que lo ha hecho a partir de su ID
       2º: Ontener el objeto de la clase Evento a partir de su ID
       3º: Obtener la fecha actual y empezar a montar un objeto de tipo DATETIME con ella
       para el campo de fecha
       4º: Si es la respuesta a un comentario, buscar el comentario al que está respondiendo
       y mandar a buscarlo también
       5º: Con todo esto, crear el comentario, añadiendo en un set de cada atributo
       el correspondiente.
       6º: Confirmar y hacer FETCH en la BD para que se guarde todo.*/
    //Ruta para modificar un evento concreto. Si es en un lugar nuevo, la latitud y longitud
    //cambian
    //Ruta para borrar un evento concreto
}
