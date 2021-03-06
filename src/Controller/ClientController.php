<?php

namespace App\Controller;

use App\Entity\Users;
use OpenApi\Annotations as OA;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @OA\Response(
 *     response=401,
 *     description="Unauthorized: Authentification et Rôle  Administrateur requis",
 *     @OA\JsonContent(default="Exemple: {'status': '401', 'message': 'Unauthorized: Authentification et Rôle  Administrateur requis'}")
 * )
 */
class ClientController extends AbstractController
{
    private $serializer;
    private $em;
    private $encoder;
    private $validator;

    public function __construct(SerializerInterface $serializer, EntityManagerInterface $em, 
    UserPasswordHasherInterface $encoder, ValidatorInterface $validator) {
        $this->serializer = $serializer;
        $this->em = $em;
        $this->encoder = $encoder;
        $this->validator = $validator;
    }
    /**
     * 
     */
    private function verif(): ?Response
    {       
        $isFullyAuthenticated = $this->container->get('security.authorization_checker')
        ->isGranted('ROLE_ADMIN');

        if (!$isFullyAuthenticated) {
            return $this->json(['status' => 401, 'message' => 'Unauthorized: Authentification et Rôle Administrateur requis'], 401);
        }
        
        return null;
    }
    /**
     * Utilisateur NON trouvé OU n'est pas lié à ce client
     */
    private function exist(int $id, $user):?Response {

        if (!$user || ($user->getClient() != $this->getUser())){
            return $this->json(['status' => 404, 'message' => 'Utilisateur avec l\'Id: '.$id.', non trouvé'], 404);
        }

        // envoi le détail d'un utilisateur
        $response = $this->json($user, 200, [],['groups' => 'user:detail']);
        return $response;
    }

    /**
     * 
     * @OA\Tag(name="Client")
     * @OA\Get(
     *      summary="Liste des utilisateurs",
     * )
     * @OA\Response(
     *     response=200,
     *     description="Retourne la liste des utilisateurs",
     *     @OA\JsonContent(ref="#/components/schemas/Utilisateur")
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="Numéro de page",
     *     required=false,
     *     )
     * )
     * @OA\Response(
     *     response=500,
     *     description="Retourne une erreur serveur",
     *     @OA\JsonContent(default="Retourne une erreur serveur")
     * )
     * 
     * @Route("/api/users/", name="app_users", methods={"GET"})
     */
    public function showAllUser(Request $request, PaginatorInterface $paginator, UsersRepository $usersRepository, CacheInterface $cache): Response
    {
            if($this->verif()) {
                // Utilisateur non connecté ou pas ADMIN   
                return $this->verif();
            }

            $client = $this->getUser();
            //Mise en cache des utilisateur trouvés
            $users = $cache->get('usersFind', function() use($usersRepository, $client){
                return $usersRepository->findAllUsers($client);
            });
            // Aucun utilisateur trouvé
            if (!$users) {
                return $this->json(['status' => 200, 'message' => 'Aucun utilisateur trouvé'], 200);
            }
            // envoi la liste des utilisateurs en json
            $itemParPager = 3;
            $currentPage = $request->query->getInt('page', 1);
            $nbPages = ceil(count($users)/$itemParPager);

            $usersPagine = $paginator->paginate(
                $users,
                $currentPage,
                $itemParPager
            );

            $response = $this->json(['Page No: '.$currentPage.' sur : '.$nbPages, 'Liste des utilisateurs', $usersPagine], 200, [],['groups' => 'user:list']);
            return $response;  
    }

    /**
     * 
     * @OA\Tag(name="Client")
     * @OA\Get(
     *      summary="Détails d'un utilisateur",
     * )
     * @OA\Response(
     *     response=200,
     *     description="Retourne le détail d'un utilisateur",
     *     @OA\JsonContent(ref="#/components/schemas/Utilisateur_Detail")
     * )
     * @OA\Response(
     *     response=404,
     *     description="Retourn utilisateur non trouvé, ou pas de route si l'Id n'est pas donné",
     *     @OA\JsonContent(default="Exemple: {'status': '404', 'message': 'Utilisateur avec l'Id: ID, non trouvé'}")
     *)
     * 
     * @Route("/api/user/{id}", name="app_user_one", methods={"GET"})
     */
    public function showOneUser(int $id, UsersRepository $UsersRepository, CacheInterface $cache): Response
    {
        if($this->verif()) {
            // Utilisateur non connecté ou pas ADMIN
            return $this->verif();
        }
        //Mise en cache de l'utilisateur trouvé
        $user = $cache->get('userFind'.$id, function() use($UsersRepository, $id){
            return $UsersRepository->find($id);
        });

        // L'utilisateur n'existe pas OU n'est pas un utilisateur lié à un client ;
        return $this->exist($id, $user);
    }

    /**
     * 
     * @OA\Tag(name="Client")
     * @OA\Post(
     *      summary="Ajout d'un utilisateur",
     * )
     * @OA\Parameter(
     *     name="Utilisateur",
     *     in="query",
     *     description="Champs utilisateur a compléter",
     *     required=true,
     *     @OA\Schema(
     *            type="object",
     *            @OA\Property(property="email", type="string", format="email"),
     *            @OA\Property(property="password", type="string", format="password")
     *         )
     *     )
     * )
     * 
     * @OA\Response(
     *     response=201,
     *     description="Utilisateur ajouté avec succès",
     *     @OA\JsonContent(ref="#/components/schemas/Utilisateur_Detail")
     * )
     * @OA\Response(
     *     response=400,
     *     description="Erreurs de Syntaxe, ou erreurs SQL",
     *     @OA\JsonContent(default="Exemple: {'status': '400', 'message': 'Erreurs de Syntaxe'}")
     * )
     * 
     * @Route("/api/user/add", name="app_user_add", methods={"POST"})
     */
    public function addUser(Request $request, UserInterface $client=null, CacheInterface $cache): Response
    {
        if($this->verif()) {
            // Utilisateur non connecté ou pas ADMIN
            return $this->verif();
        }
        // Obtenir les informations saisies
        $jsonRecu = $request->getContent();
        
        try{

        // Deserializer les informations
        $user = $this->serializer->deserialize($jsonRecu, Users::class, 'json');
        $user->setClient($client);

        // Gestion des erreurs SQL
        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        // encoder le mot de pass
        $encoded = $this->encoder->hashPassword($user, $user->getPassword());
        $user->setPassword($encoded);
        

        // enregistrer dans la BD
        $this->em->persist($user);
        $this->em->flush();
        // On supprime le cache liste des utilisateurs
        $cache->delete('usersFind');

        // Envoyer la reponse (cas  valide)
        $response = $this->json(['Utilisateur ajouté avec succès', $user], 201, [],['groups' => 'user:detail']);
        return $response;

        } catch(\Exception $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }  
    }

    /**
     * 
     * @OA\Tag(name="Client")
     * @OA\Delete(
     *      summary="Suppression d'un utilisateur",
     * )
     * 
     * @OA\Response(
     *     response=200,
     *     description="Retourne 'Utilisateur supprimé'",
     * )
     * @OA\Response(
     *     response=404,
     *     description="Retourne 'Utilisateur avec l\'Id: ID, non trouvé', ou 'Route non trouvé si pas d\'ID'",
     *     @OA\JsonContent(default="Exemple: {'status': '404', 'message': 'Utilisateur avec l'Id: ID, non trouvé'}")
     * )
     * 
     * @Route("/api/user/delete/{id}", name="app_user_delete", methods={"DELETE"})
     */
    public function deleteUser(int $id, ManagerRegistry $doctrine, UserInterface $client=null, CacheInterface $cache): Response
    {
        if($this->verif()) {
            // Utilisateur non connecté ou pas ADMIN   
            return $this->verif();
        }

        $repoUser = $doctrine->getRepository(Users::class);
        $user = $repoUser->find($id);
        // Utilisateur lié à un client trouvé;
        if ($user && ($user->getClient() == $client)) {
            $em = $doctrine->getManager();
            $em->remove($user);
            $em->flush();
            // On supprime le cache liste des utilisateurs
            $cache->delete('usersFind');

            // On envoi la réponse
            return $this->json(['status' => 200, 'message' => 'Utilisateur supprimé'], 200);
        }
        // Utilisateur NON trouvé OU n'est pas lié à ce client;
        return $this->json(['status' => 404, 'message' => 'Utilisateur avec l\'Id: '.$id.', non trouvé'], 404);
    }

    /**
     * 
     * @OA\Tag(name="Client")
     * @OA\Put(
     *      summary="Editer un utilisateur",
     * )
     * @OA\Response(
     *     response=201,
     *     description="Utilisateur modifié avec succès",
     * )
     * @OA\Response(
     *     response=400,
     *     description="Erreurs de Syntaxe, ou erreurs SQL",
     *     @OA\JsonContent(default="Exemple: {'status': '400', 'message': 'Erreurs de Syntaxe'}")
     * 
     * )
     * @OA\Response(
     *     response=404,
     *     description= "Utilisateur avec l'Id: ID, non trouvé",
     *     @OA\JsonContent(default="Exemple: {'status': '404', 'message': 'Utilisateur avec l'Id: ID, non trouvé'}")
     * )
     * 
     * @Route("/api/user/edit/{id}", name="app_user_edit", methods={"PUT"})
     */
    public function editUser(int $id, Request $request, ManagerRegistry $doctrine, CacheInterface $cache): Response
    {
        if($this->verif()) {
            // Utilisateur non connecté ou pas ADMIN   
            return $this->verif();
        }

            $repoUser = $doctrine->getRepository(Users::class);
            $user = $repoUser->find($id);
            $client = $this->getUser();
            // Utilisateur non trouvé           
            if (!$user || ($user->getClient() != $client)){
                return $this->json(['status' => 404, 'message' => 'Utilisateur avec l\'Id: '.$id.', non trouvé'], 404);
            }

            // Utilisateur trouvé - Obtenir les informations saisies
            $jsonRecu = $request->getContent();

            try{
            // Deserializer les informations
            $usermodified = $this->serializer->deserialize($jsonRecu, Users::class, 'json');
            // reprendre l'ID de l'Admin
            $usermodified->setClient($user->getClient());
            $errors = $this->validator->validate($usermodified);

            if (count($errors) > 0) {
                return $this->json($errors, 400);
            }

            // encoder le mot de pass
            $encoded = $this->encoder->hashPassword($usermodified, $usermodified->getPassword());

            $user->setPassword($encoded);
            $user->setEmail($usermodified->getEmail());

            // enregistrer dans la BD
            $this->em->persist($user);
            $this->em->flush();
            // On supprime le cache liste des utilisateurs et le cache utilisateur ID
            $cache->delete('usersFind');
            $cache->delete('userFind'.$id);

            // Envoyer la reponse (cas  valide)
            $response = $this->json(['Utilisateur modifié avec succès', $user], 201, [],['groups' => 'user:detail']);

            return $response;

            } catch(\Exception $e) {
                return $this->json([
                    'status' => 400,
                    'message' => $e->getMessage()
                ], 400);
            }
    }
}
