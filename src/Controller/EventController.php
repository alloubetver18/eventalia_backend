<?php

namespace App\Controller;

use App\Entity\Events;
use App\Entity\Places;
use App\Entity\Themes;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;

#[Route('/event')]
class EventController extends AbstractController
{
    #[Route(path: '/list', name: 'event_list', methods: ['GET'])]
    public function eventList(EntityManagerInterface $entityManager): JsonResponse
    {
        // Tomando el repositio de la entidad Events, usamos la función findAll para obtener todos
        //   los eventos. 
        $eventos = $entityManager->getRepository(Events::class)->findAll();
        // Creamos una nueva respuesta JSON 
        $json = new JsonResponse();
        // Creamos un array en el cual almacenaremos los eventos 
        $eventosArray = [];
        // Si no se ha encontrado ningún evento, devolverá un resultado de fallo y un array vacio de datos 
        if (empty($eventos)) {
            $json->setData(['result' => 'failure', 'message' => 'No hay eventos', 'data' => []]);
        } else {
            // Si se ha encontrado al menos un evento, empezará a generar una respuesta 
            // Para cada uno de los eventos, asociado a una variable $evento... 
            foreach ($eventos as $evento) {

                $arrayThemes = [];

                $themes = $evento->getEventThemes();
                foreach ($themes as $theme) {
                    $arrayThemes[] = [
                        'id' => $theme->getId(),
                        'denominacion' => $theme->getDenomination(),
                    ];
                }

                $eventosArray[] = [
                    'event_id' => $evento->getId(),
                    'nombre' => $evento->getEventName(),
                    'organizador' => $evento->getEventCreatedBy()->getOrganizations()->getOrganizationName(),
                    'lugar' => $evento->getPlace()->getPlacename(),
                    'direccion' => $evento->getPlace()->getAddress(),
                    'ciudad' => $evento->getPlace()->getCity(),
                    'provincia' => $evento->getPlace()->getProvince(),
                    'latitud' => $evento->getPlace()->getLatitude(),
                    'longitud' => $evento->getPlace()->getLongitude(),
                    'generos' => $arrayThemes,
                    'fecha_inicio' => $evento->getFromdate()->format('Y-m-d'),
                    'hora_inicio' => $evento->getStartat()->format('H:i:s'),
                    'fecha_fin' => $evento->getTodate()->format('Y-m-d'),
                    'descripcion' => $evento->getDescription(),
                    'price' => $evento->getPrice(),
                    'imageformat' => $evento->getImageEventformat(),
                    'imagen' => base64_encode(stream_get_contents($evento->getImageEvent())),

                    // Agrega más campos según sea necesario
                ];
            }
            $json->setData(['result' => 'success', 'data' => $eventosArray]);
        }

        return $json;
    }

    #[Route(path: '/createdby/{email}', name: 'event_created_by', methods: ['GET'])]
    public function getEventsCreatedBy(EntityManagerInterface $entityManager, $email): JsonResponse
    {
        $user = $entityManager->getRepository(Users::class)->findOneBy([
            'email' => $email
        ]);

        $json = new JsonResponse();
        if (empty($user)) {
            $json->setData(['result' => 'failure', 'message' => 'No existe ningún usuario común con dicho email', 'data' => []]);
        } else {

            $events = $entityManager->getRepository(Events::class)->findBy(
                [
                    'eventcreatedBy' => $user
                ]
            );

            if (empty($events)) {
                $json->setData(['result' => 'success', 'data' => []]);
            } else {
            }


            $json->setData(['result' => 'success', 'data' => $user->getRol()]);
        }

        return $json;
    }

    //Ruta para los datos de un evento concreto
    #[Route(path: '/data/{id}/{email}', name: 'event_info', methods: ['GET'])]
    public function getEventData(Request $request, EntityManagerInterface $entityManager, $id, $email): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $participating = false;

        if (!empty($email)) {
            $user = $entityManager->getRepository(Users::class)->findOneBy([
                'email' => $email
            ]);
        }




        $evento = $entityManager->getRepository(Events::class)->find($id);
        $json = new JsonResponse();
        if (empty($evento)) {
            $json->setData(['result' => 'failure', 'message' => 'No existe ningún evento con dicha id', 'data' => []]);
        } else {
            if (!empty($user)) {
                if ($evento->getParticipatingIn()->contains($user)) {
                    $participating = true;
                }
            }

            $arrayThemes = [];
            $themes = $evento->getEventThemes();
            foreach ($themes as $theme) {
                $arrayThemes[] = [
                    'id' => $theme->getId(),
                    'denominacion' => $theme->getDenomination(),
                ];
            }
            $datosEvento = [
                'nombre' => $evento->getEventName(),
                'organizador' => $evento->getEventCreatedBy()->getOrganizations()->getOrganizationName(),
                'id_organizador' => $evento->getEventCreatedBy()->getOrganizations()->getId(),
                'lugar' => $evento->getPlace()->getPlacename(),
                'direccion' => $evento->getPlace()->getAddress(),
                'ciudad' => $evento->getPlace()->getCity(),
                'latitud' => $evento->getPlace()->getLatitude(),
                'longitud' => $evento->getPlace()->getLongitude(),
                'generos' => $arrayThemes,
                'fecha_inicio' => $evento->getFromdate()->format('Y-m-d'),
                'hora_inicio' => $evento->getStartat()->format('H:i:s'),
                'fecha_fin' => $evento->getTodate()->format('Y-m-d'),
                'descripcion' => $evento->getDescription(),
                'price' => $evento->getPrice(),
                'imageformat' => $evento->getImageEventformat(),
                'image' => base64_encode(stream_get_contents($evento->getImageEvent())),
                'participating' => $participating,
            ];
            $json->setData(['result' => 'success', 'data' => $datosEvento]);
        }
        return $json;
    }
    //Ruta para añadir un evento nuevo: incluye crear el evento, y el lugar donde

    #[Route(path: '/add', name: 'event_add', methods: ['POST'])]
    public function addEvent(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger): JsonResponse
    {
        //HACER PRUEBAS EN POSTMAN
        $json = new JsonResponse();
        //var_dump($request->request);

        $data = json_decode($request->getContent(), true);

        //Comprobar si el email se ha utilizado ya o no


        $logger->info($data['Name']);
        $logger->info($data['Description']);
        $logger->info($data['CreatedBy']);
        $logger->info($data['themes']);
        $logger->info($data['eventImage']);
        $logger->info($data['eventImageFormat']);
        $logger->info($data['Place']["name"]);
        $logger->info($data['Place']["address"]);
        $logger->info($data['Place']["lonlat"]);
        $logger->info($data['Place']["localidad"]);
        $logger->info($data['Place']["provincia"]);
        $logger->info($data['from']);
        $logger->info($data['to']);
        $logger->info($data['hour']);
        $logger->info($data['price']);

        //Crear un nuevo usuario para asociarlo después a un nuevo usuario común
        $newPlace = new Places;



        /* Crear nuevo lugar */


        /**/
        /**/

        $newPlace->setPlacename($data['Place']['name']);
        $newPlace->setAddress($data['Place']['address']);
        $newPlace->setCity($data['Place']["localidad"]);
        $newPlace->setProvince($data['Place']["provincia"]);
        $newPlace->setWebpage("ninguna");
        $newPlace->setEmail("ninguno");

        $coordinatesArray = explode(",", $data['Place']["lonlat"]);

        $newPlace->setLatitude($coordinatesArray[0]);
        $newPlace->setLongitude($coordinatesArray[1]);

        $entityManager->persist($newPlace);

        $entityManager->flush();

        $newEvent = new Events;

        $newEvent->setPlace($newPlace);
        $newEvent->setEventName($data['Name']);
        $newEvent->setFromdate(\DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $data['from']));
        $newEvent->setTodate(\DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $data['to']));
        $newEvent->setStartat(\DateTime::createFromFormat('H:i', $data['hour']));
        $newEvent->setDescription($data['Description']);
        $newEvent->setPrice($data['price']);


        $newEvent->setImageEvent(base64_decode($data['eventImage']));
        $newEvent->setImageEventformat($data['eventImageFormat']);


        $createdBy = $entityManager->getRepository(Users::class)->findOneBy([
            'email' => $data['CreatedBy']
        ]);

        if (!empty($createdBy)) {
            $newEvent->setEventCreatedBy($createdBy);
        } else {
            $json->setData(['result' => 'failure']);

            return $json;
        }

        $themesArrayId = explode(",", $data['themes']);

        /**/
        foreach ($themesArrayId as $themeId) {
            $newTheme = $entityManager->getRepository(Themes::class)->find(intval($themeId));
            $newEvent->addEventTheme($newTheme);
        }

        $entityManager->persist($newEvent);

        $entityManager->flush();

        //Añadir desde el cliente los datos a recibir.

        $json->setData(['result' => 'success']);

        return $json;
    }

    #[Route(path: '/follow', name: 'event_follow', methods: ['POST'])]
    public function followEvent(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger): JsonResponse
    {
        $json = new JsonResponse();
        //var_dump($request->request);

        $data = json_decode($request->getContent(), true);

        $logger->info($data['email']);
        $logger->info($data['eventId']);

        /**/
        $evento = $entityManager->getRepository(Events::class)->find($data['eventId']);

        $logger->info($evento->getEventName());

        $user = $entityManager->getRepository(Users::class)->findOneBy([
            'email' => $data['email']
        ]);

        $logger->info($user->getNick());

        $logger->info("Usuario " . $user->getNick() . " participa en evento " . $evento->getEventName() . ": " . ($evento->getParticipatingIn()->contains($user) ? 'true' : 'false'));

        if ($evento->getParticipatingIn()->contains($user)) {
            $logger->info("Usuario Participa. Se precisa retirada");
            $evento->removeParticipatingIn($user);
            $user->removeEventsParticipatedIn($evento);
            $logger->info("Usuario Retirado.");
        } else {
            $logger->info("Usuario No Participa. Se precisa ingreso");
            $evento->addParticipatingIn($user);
            $user->addEventsParticipatedIn($evento);
            $logger->info("Usuario Añadido.");
        }  /**/

        $entityManager->persist($evento);

        $entityManager->flush();

        $entityManager->persist($user);

        $entityManager->flush();



        $json->setData(['result' => 'success']);

        return $json;
    }
    //ocurre, si su latitud y longitud no existen en el sistema
    //Ruta para modificar un evento concreto. Si es en un lugar nuevo, la latitud y longitud
    //cambian
    //Ruta para borrar un evento concreto
}
