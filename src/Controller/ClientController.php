<?php

namespace App\Controller;

use App\Entity\Users;
use OpenApi\Annotations as OA;
use Doctrine\ORM\EntityManager;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
// use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClientController extends AbstractController
{
    /**
     * 
     * @OA\Tag(name="Client")
     * @OA\Get(
     *      summary="Liste des utilisateurs",
     * )
     * 
     * @Route("/api/users/", name="app_users", methods={"GET"})
     */
    public function showAllUser(UsersRepository $usersRepository): Response
    {
        $product = $usersRepository->findAll();
        
        $response = $this->json($product, 200, [],[]);

        return $response;
    }

    /**
     * 
     * @OA\Tag(name="Client")
     * @OA\Get(
     *      summary="Détails d'un utilisateur",
     * )
     * 
     * @Route("/api/user/{id}", name="app_user", methods={"GET"})
     */
    public function showOneUser($id, UsersRepository $UsersRepository): Response
    {
        $product = $UsersRepository->find($id);
        
        $response = $this->json($product, 200, [],[]);

        return $response;
    }

    /**
     * 
     * @OA\Tag(name="Client")
     * @OA\Post(
     *      summary="Ajout d'un utilisateur",
     * )
     * 
     * 
     * @Route("/api/user/add", name="app_user_add", methods={"POST"})
     */
    public function addUser(Request $request, SerializerInterface $serializer, 
    EntityManagerInterface $em, UserPasswordHasherInterface $encoder, ValidatorInterface $validator): Response
    {
        // Obtenir les informations saisies
        $jsonRecu = $request->getContent();

        try{

        // Deserializer les informations
        $user = $serializer->deserialize($jsonRecu, Users::class, 'json');

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        // encoder le mot de pass
        $encoded = $encoder->hashPassword($user, $user->getPassword());
        $user->setPassword($encoded);

        // enregistrer dans la BD
        $em->persist($user);
        $em->flush();

        // Envoyer la reponse (cas  valide)
        $response = $this->json('Utilisateur ajouté avec succès', 201, [],[]);

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
     * )
     * 
     * @Route("/api/user/delete/{id}", name="app_user", methods={"DELETE"})
     */
    public function deleteUser($id, ManagerRegistry $doctrine): Response
    {
        $repoUser = $doctrine->getRepository(Users::class);
        $user = $repoUser->find($id);

        if ($user) {
            $em = $doctrine->getManager();
            $em->remove($user);
            $em->flush();

            $response = $this->json("Utilisateur supprimé", 200, [],[]);

            return $response;
        }
        
        $response = $this->json('Utilisateur avec l\'Id: '.$id.', non trouvé', 404, [],[]);

        return $response;
    }

    /**
     * 
     * @OA\Tag(name="Client")
     * @OA\Put(
     *      summary="Editer un utilisateur",
     * )
     * 
     * 
     * @Route("/api/user/edit/{id}", name="app_user_edit", methods={"PUT"})
     */
    public function editUser($id, Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine,
    EntityManagerInterface $em, UserPasswordHasherInterface $encoder, ValidatorInterface $validator): Response
    {
        $repoUser = $doctrine->getRepository(Users::class);
        $user = $repoUser->find($id);
 
        // Utilisateur non trouvé
        
        if (!$user) {

            $response = $this->json("Utilisateur avec l'Id: ".$id.", non trouvé", 404, [],[]);
            return $response;
        }

        // Utilisateur trouvé
        // Obtenir les informations saisies
        $jsonRecu = $request->getContent();

        try{

        // Deserializer les informations
        $usermodified = $serializer->deserialize($jsonRecu, Users::class, 'json');
        // dd($usermodified->getEmail());

        $errors = $validator->validate($usermodified);

        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        // encoder le mot de pass
        $encoded = $encoder->hashPassword($usermodified, $usermodified->getPassword());
        // var_dump($user, $usermodified);
        $user->setPassword($encoded);
        $user->setEmail($usermodified->getEmail());



        // enregistrer dans la BD
        $em->persist($user);
        $em->flush();

        // Envoyer la reponse (cas  valide)
        $response = $this->json('Utilisateur modifié avec succès', 201, [],[]);

        return $response;

        } catch(\Exception $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }

    }

}
