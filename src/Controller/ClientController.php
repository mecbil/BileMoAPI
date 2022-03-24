<?php

namespace App\Controller;

use OpenApi\Annotations as OA;
use App\Repository\UsersRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
}
