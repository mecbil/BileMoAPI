<?php

namespace App\Controller;

use App\Entity\Users;
use OpenApi\Annotations as OA;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
// use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ClientController extends AbstractController
{
    /**
     * 
     * @OA\Tag(name="Client")
     * 
     * @Route("/api/users/", name="app_users", methods={"GET"})
     */
    public function sowAllUser(UsersRepository $usersRepository): Response
    {
        $product = $usersRepository->findAll();
        
        $response = $this->json($product, 200, [],[]);

        return $response;
    }

    /**
     * 
     * @OA\Tag(name="Client")
     * 
     * @Route("/api/user/{id}", name="app_user", methods={"GET"})
     */
    public function sowOneUser($id, UsersRepository $UsersRepository): Response
    {
        $product = $UsersRepository->find($id);
        
        $response = $this->json($product, 200, [],[]);

        return $response;
    }

    /**
     * 
     * @OA\Tag(name="Client")
     * 
     * 
     * @Route("/api/user/add", name="app_user_add", methods={"POST"})
     */
    public function addUser(Request $request, SerializerInterface $serializer, 
    EntityManagerInterface $em, UserPasswordHasherInterface $encoder, ValidatorInterface $validator): Response
    {
        // Obtenir les informations
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
}
