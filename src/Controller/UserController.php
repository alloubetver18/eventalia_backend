<?php

namespace App\Controller;

use App\Classes\CustomCommonUser;
use App\Entity\Generos;
use App\Entity\Organizations;
use App\Entity\Users;
use App\Entity\CommonUsers;
use App\Entity\Themes;
use Doctrine\DBAL\Events;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Psr\Log\LoggerInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    public function __construct(JWTTokenManagerInterface $jwtManager)
    {
    }


    #[Route(path: '/getrol/{email}', name: 'user_rol', methods: ['GET'])]
    public function getUserRol($email, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $entityManager->getRepository(Users::class)->findOneBy([
            'email' => $email
        ]);

        $json = new JsonResponse();
        if (empty($user)) {
            $json->setData(['result' => 'failure', 'message' => 'No existe ningún usuario común con dicho email', 'data' => []]);
        } else {
            $json->setData(['result' => 'success', 'data' => $user->getRol()]);
        }

        return $json;
    }

    /* Todos los usuarios comunes */
    #[Route(path: '/common', name: 'user_list', methods: ['GET'])]
    public function commonUsersList(EntityManagerInterface $entityManager): JsonResponse
    {
        $commonUsersCollection = $entityManager->getRepository(CommonUsers::class)->findAll();
        $commonUsersArray = [];
        foreach ($commonUsersCollection as $commonUser) {
            $userFriends = [];
            $organizationsFollowed = [];
            $eventsCreatedArray = [];
            $eventsInterestedArray = [];

            $name = $commonUser->getName();
            $surname = $commonUser->getSurname();

            $nick = $commonUser->getUser()->getNick();
            $password = $commonUser->getUser()->getPassword();
            $email = $commonUser->getUser()->getEmail();
            $rol = $commonUser->getUser()->getRol();
            $imageFormat = $commonUser->getUser()->getAvatarimageformat();
            $imageString = base64_encode(stream_get_contents($commonUser->getUser()->getAvatarimage()));

            $organizations = $commonUser->getFollowing();
            foreach ($organizations as $organization) {
                $organizationsFollowed[] = [
                    'name' => $organization->getOrganizationName()
                ];
            }

            $eventsCreated = $commonUser->getUser()->getEventsCreated();
            foreach ($eventsCreated as $event) {
                $eventsCreatedArray[] = [
                    'name' => $event->getEventName(),
                ];
            }

            $eventsInterested = $commonUser->getUser()->getEventsParticipatedIn();
            foreach ($eventsInterested as $event) {
                $eventsInterestedArray[] = [
                    'name' => $event->getEventName(),
                ];
            }

            $friendList = $commonUser->getCommonUsersFriends();
            foreach ($friendList as $friend) {
                $userFriends[] = [
                    'name' => $friend->getName(),
                ];
            }

            $commonUsersArray[] = [
                'name' => $name,
                'surname' => $surname,
                'nick' => $nick,
                'password' => $password,
                'email' => $email,
                'rol' => $rol,
                'organizationsFollowed' => $organizationsFollowed,
                'eventsCreated' => $eventsCreatedArray,
                'eventsInterested' => $eventsInterestedArray,
                'friends' => $userFriends,
                'imageFormat' => $imageFormat,
                //'imageData' => $imageString
            ];
            //echo is_resource($blobStream);
            //echo "<img src = data:image/png;base64," . base64_encode(stream_get_contents($blobStream)) . " style='height: 100px; width: 100px;'>";

        }
        $json = new JsonResponse();
        $json->setData(['result' => 'success', 'data' => $commonUsersArray]);
        $json->headers->set('Access-Control-Allow-Origin', '*');
        return $json;
    }

    /* Datos de 1 solo usuario común */

    #[Route(path: '/common/getone', name: 'common_user_info', methods: ['POST'])]
    public function getCommonUserData(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger): JsonResponse
    {

        //HACER PRUEBAS EN POSTMAN
        $json = new JsonResponse();

        date_default_timezone_set('Europe/Madrid');

        $momentoactual = date('Y-m-d');

        $contenido = $request->getContent();


        $data = json_decode($contenido, true);
        $logger->info($contenido);

        $user = $entityManager->getRepository(Users::class)->findOneBy([
            'email' => $contenido
        ]);

        $json = new JsonResponse();
        if (empty($user)) {
            $json->setData(['result' => 'failure', 'message' => 'No existe ningún usuario común con dicho email', 'data' => []]);
        } else {
            if ($user->getRol() == 1) {
                $eventsInterestedArray = [];
                $eventsInterestedIdArray = [];
                $eventsInterested = $user->getEventsParticipatedIn();
                if (!empty($eventsInterested)) {
                    foreach ($eventsInterested as $event) {
                        array_push($eventsInterestedIdArray, $event->getId());
                        $ended = "";
                        if ($momentoactual < $event->getTodate()->format('Y-m-d')) {
                            $ended = "not Ended";
                            $eventsInterestedArray[] = [
                                'id' => $event->getId(),
                                'name' => $event->getEventName(),
                                'image' => base64_encode(stream_get_contents($event->getImageEvent())),
                                'imageformat' => $event->getImageEventformat(),
                                'created_by' => $event->getEventCreatedBy()->getOrganizations()->getOrganizationName(),
                                'date_when_started' => $event->getFromdate()->format('Y-m-d'),
                                'date_when_finish' => $event->getTodate()->format('Y-m-d'),
                                'city' => $event->getPlace()->getCity(),
                                'province' => $event->getPlace()->getProvince(),
                                'todaydate' => $momentoactual,
                                'eventhappened' => $ended,
                            ];
                        } else if ($momentoactual > $event->getTodate()->format('Y-m-d')) {
                            $ended = "Ended";
                        } else {
                            $ended = "Is Now";
                        }
                    }
                }

                $eventsByThemeArray = [];
                $eventsIdByThemeArray = [];
                $themesInterestedArray = [];
                $themesInterested = $user->getThemesInterested();
                $themesInterestedId = [];
                $eventsSuggestedIdArray = [];
                $eventsSuggested = [];
                foreach ($themesInterested as $theme) {
                    $eventsByThemes = $theme->getEvents();
                    foreach ($eventsByThemes as $newEvent) {
                        if (!in_array($newEvent->getId(), $eventsIdByThemeArray, true)) {
                            array_push($eventsIdByThemeArray, $newEvent->getId());
                            if (!in_array($newEvent->getId(), $eventsInterestedIdArray, true)) {
                                $ended = "";
                                if ($momentoactual > $newEvent->getTodate()->format('Y-m-d')) {
                                    $ended = "Ended";
                                } else if ($momentoactual < $newEvent->getTodate()->format('Y-m-d')) {
                                    $ended = "Not Ended";
                                    $eventsByThemeArray[] = [
                                        'id' => $newEvent->getId(),
                                        'name' => $newEvent->getEventName(),
                                        'image' => base64_encode(stream_get_contents($newEvent->getImageEvent())),
                                        'imageformat' => $newEvent->getImageEventformat(),
                                        'created_by' => $newEvent->getEventCreatedBy()->getOrganizations()->getOrganizationName(),
                                        'date_when_started' => $newEvent->getFromdate()->format('Y-m-d'),
                                        'date_when_finish' => $newEvent->getTodate()->format('Y-m-d'),
                                        'city' => $newEvent->getPlace()->getCity(),
                                        'province' => $newEvent->getPlace()->getProvince(),
                                        'todaydate' => $momentoactual,
                                        'eventhappened' => $ended,
                                    ];
                                } else {
                                    $ended = "Is Now";
                                }
                            }
                        }
                    }

                    $themesInterestedArray[] = [
                        'id' => $theme->getId(),
                        'name' => $theme->getDenomination(),
                    ];
                }

                $userdata = [
                    'request-at' => $momentoactual,
                    'id-user' => $user->getId(),
                    'nick' => $user->getNick(),
                    'name' => $user->getCommonUsers()->getName(),
                    'surname' => $user->getCommonUsers()->getSurname(),
                    'email' => $user->getEmail(),
                    'rol' => $user->getRol(),
                    'themes' => $themesInterestedArray,
                    'eventsidbytheme' => $eventsIdByThemeArray,
                    'eventsinterestedidarray' => $eventsInterestedIdArray,
                    'eventsinterested' => $eventsInterestedArray,
                    'eventssuggestedidarray' => $eventsSuggestedIdArray,
                    'eventssugested' => $eventsByThemeArray,
                    'avatarimage' => base64_encode(stream_get_contents($user->getAvatarimage())),
                    'avatarimageformat' => $user->getAvatarimageformat(),
                ];
                $json->setData(['result' => 'success', 'data' => $userdata]);
            } else if ($user->getRol() == 2) {
                $eventsCreatedArray = [];
                $eventsCreatedIdArray = [];
                $themesInterestedArray = [];
                $themesInterested = $user->getThemesInterested();
                $themesInterestedId = [];
                foreach ($themesInterested as $theme) {
                    $themesInterestedArray[] = [
                        'id' => $theme->getId(),
                        'name' => $theme->getDenomination(),
                    ];
                }

                $eventsCreated = $user->getEventsCreated();
                if (!empty($eventsCreated)) {
                    foreach ($eventsCreated as $event) {
                        array_push($eventsCreatedIdArray, $event->getId());
                        $ended = "";
                        if ($momentoactual < $event->getTodate()->format('Y-m-d')) {
                            $ended = "not Ended";
                            $eventsCreatedArray[] = [
                                'id' => $event->getId(),
                                'name' => $event->getEventName(),
                                'image' => base64_encode(stream_get_contents($event->getImageEvent())),
                                'imageformat' => $event->getImageEventformat(),
                                'created_by' => $event->getEventCreatedBy()->getOrganizations()->getOrganizationName(),
                                'date_when_started' => $event->getFromdate()->format('Y-m-d'),
                                'date_when_finish' => $event->getTodate()->format('Y-m-d'),
                                'city' => $event->getPlace()->getCity(),
                                'province' => $event->getPlace()->getProvince(),
                                'todaydate' => $momentoactual,
                                'eventhappened' => $ended,
                            ];
                        } else if ($momentoactual > $event->getTodate()->format('Y-m-d')) {
                            $ended = "Ended";
                        } else {
                            $ended = "Is Now";
                        }
                    }
                }
                $userdata = [
                    'request-at' => $momentoactual,
                    'id-user' => $user->getId(),
                    'nick' => $user->getNick(),
                    'name' => $user->getOrganizations()->getOrganizationName(),
                    'webpage' => $user->getOrganizations()->getOrganizationWebpage(),
                    'description' => $user->getOrganizations()->getOrganizationDescription(),
                    'email' => $user->getEmail(),
                    'themes' => $themesInterestedArray,
                    'rol' => $user->getRol(),
                    'eventscreated' => $eventsCreatedArray,
                    'avatarimage' => base64_encode(stream_get_contents($user->getAvatarimage())),
                    'avatarimageformat' => $user->getAvatarimageformat(),
                ];
                $json->setData(['result' => 'success', 'data' => $userdata]);
            }
        }
        return $json;
    }

    /* Login de usuario */
    /* En función de su Rol, devolverá los datos de un usuario común o de una organización,
       O solo los del usuario, porque será un administrador */

    #[Route(path: '/organizations', name: 'organization_list', methods: ['GET'])]
    public function organizationsList(EntityManagerInterface $entityManager): JsonResponse
    {
        $organizationsCollection = $entityManager->getRepository(Organizations::class)->findAll();
        $organizationsArray = [];
        foreach ($organizationsCollection as $organization) {
            $userFollowers = [];
            $ThemesArray = [];
            $eventsCreatedArray = [];

            $name = $organization->getOrganizationName();
            $description = $organization->getOrganizationDescription();
            $webpage = $organization->getOrganizationWebpage();
            $nick = $organization->getUser()->getNick();
            $password = $organization->getUser()->getPassword();
            $email = $organization->getUser()->getEmail();
            $rol = $organization->getUser()->getRol();

            $followers = $organization->getCommonUsersFollowers();
            foreach ($followers as $follower) {
                $userFollowers[] = [
                    'name' => $follower->getName(),
                ];
            }

            $themesPromoted = $organization->getUser()->getThemesInterested();
            foreach ($themesPromoted as $theme) {
                $ThemesArray[] = [
                    'name' => $theme->getDenomination(),
                ];
            }

            $eventsCreatedBy = $organization->getUser()->getEventsCreated();
            foreach ($eventsCreatedBy as $event) {
                $eventsCreatedArray[] = [
                    'name' => $event->getEventName(),
                ];
            }

            $imageFormat = $organization->getUser()->getAvatarimageformat();
            $imageString = base64_encode(stream_get_contents($organization->getUser()->getAvatarimage()));

            $organizationsArray[] = [
                'name' => $name,
                'description' => $description,
                'webpage' => $webpage,
                'nick' => $nick,
                'password' => $password,
                'email' => $email,
                'rol' => $rol,
                'followers' => $userFollowers,
                'themesPromoted' => $ThemesArray,
                'events' => $eventsCreatedArray,
                'imageFormat' => $imageFormat,
                //'imageData' => $imageString
            ];
            //echo is_resource($blobStream);
            //echo "<img src = data:image/png;base64," . base64_encode(stream_get_contents($blobStream)) . " style='height: 100px; width: 100px;'>";

        }
        $json = new JsonResponse();
        $json->setData(['result' => 'success', 'data' => $organizationsArray]);
        return $json;
    }

    #[Route(path: '/organizations/{id}', name: 'organization_data', methods: ['GET'])]
    public function getOrganizationData($id, EntityManagerInterface $entityManager, LoggerInterface $logger): JsonResponse
    {

        //HACER PRUEBAS EN POSTMAN
        $json = new JsonResponse();

        $organization = $entityManager->getRepository(Organizations::class)->find($id);

        if (empty($organization)) {
            $json->setData(['result' => 'failure', 'message' => 'No existe ninguna organización con dicha id', 'data' => []]);
        } else {
            $organization_id = $organization->getId();
            $name = $organization->getOrganizationName();
            $description = $organization->getOrganizationDescription();
            $webpage = $organization->getOrganizationWebpage();
            $email = $organization->getUser()->getEmail();
            $image = base64_encode(stream_get_contents($organization->getUser()->getAvatarimage()));
            $imageformat = $organization->getUser()->getAvatarimageformat();

            $organizationdata = [
                'id' => $organization_id,
                'name' => $name,
                'description' => $description,
                'webpage' => $webpage,
                'email' => $email,
                'image' => $image,
                'imageformat' => $imageformat,
            ];

            //$logger->info($organization->getId());

            $json->setData(['result' => 'success', 'data' => $organizationdata]);
        }




        return $json;
    }

    //Ruta para añadir nuevo usuario común
    /**/
    #[Route(path: '/common/add', name: 'add_common_user', methods: ['POST'])]
    public function registerUser(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        //HACER PRUEBAS EN POSTMAN
        $json = new JsonResponse();
        //var_dump($request->request);

        $data = json_decode($request->getContent(), true);

        //Comprobar si el email se ha utilizado ya o no

        //Crear un nuevo usuario para asociarlo después a un nuevo usuario común
        /**/
        $newUser = new Users;
        $newUser->setNick($data['nick']);
        $newUser->setPassword(password_hash($data['password'], PASSWORD_DEFAULT));
        $newUser->setEmail($data['email']);
        $newUser->setRol(intval($data['rol']));
        $newUser->setAvatarimage(base64_decode($data['imagenavatar']));
        $newUser->setAvatarimageformat($data['imagenavatarformat']);

        /**/
        $themesArrayId = explode(",", $data['themes']);

        /**/
        foreach ($themesArrayId as $themeId) {
            $newTheme = $entityManager->getRepository(Themes::class)->find(intval($themeId));
            $newUser->addThemesInterested($newTheme);
        }

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        /* */
        $entityManager->persist($newUser);

        // actually executes the queries (i.e. the INSERT query)
        /**/
        $entityManager->flush();

        //Crear un nuevo usuario comun
        /**/
        $newCommonUser = new CommonUsers;

        /**/
        $newCommonUser->setUser($newUser);
        $newCommonUser->setName($data['name']);
        $newCommonUser->setSurname($data['surname']);

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        /**/
        $entityManager->persist($newCommonUser);

        // actually executes the queries (i.e. the INSERT query)
        /**/
        $entityManager->flush();
        //Crear nuevo usuario, asociado al usuarioComún
        //Guardar los datos

        $json->setData(['result' => 'success']);

        return $json;
    }
    //Ruta para añadir nueva organización
    /**/
    #[Route(path: '/organizations/add', name: 'add_organization', methods: ['POST'])]
    public function registerOrganization(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        //HACER PRUEBAS EN POSTMAN
        $json = new JsonResponse();

        //Comprobar si el email se ha utilizado ya o no

        $data = json_decode($request->getContent(), true);

        //Crear un nuevo usuario para asociarlo después a un nuevo usuario común
        $newUser = new Users;
        $newUser->setNick($data['email']);
        $newUser->setPassword(password_hash($data['password'], PASSWORD_DEFAULT));
        $newUser->setEmail($data['email']);
        $newUser->setRol(intval($data['rol']));
        $newUser->setAvatarimage(base64_decode($data['imagenavatar']));
        $newUser->setAvatarimageformat($data['imagenavatarformat']);

        $themesArrayId = explode(",", $data['themes']);

        /**/
        foreach ($themesArrayId as $themeId) {
            $newTheme = $entityManager->getRepository(Themes::class)->find(intval($themeId));
            $newUser->addThemesInterested($newTheme);
        }

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($newUser);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        //Crear un nuevo usuario comun
        $newOrganization = new Organizations;

        $newOrganization->setUser($newUser);
        $newOrganization->setOrganizationName($data['name']);
        $newOrganization->setOrganizationDescription($data['description']);
        $newOrganization->setOrganizationWebpage($data['webpage']);

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($newOrganization);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();
        //Crear nuevo usuario, asociado al usuarioComún
        //Guardar los datos

        $json->setData(['result' => 'success']);
        return $json;
    }

    //Ruta para modificar usuario común
    //Ruta para modificar organización
    //Ruta para borrar usuario común
    //Ruta para borrar organización
}
