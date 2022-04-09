<?php

namespace App\Controller;

use OpenApi\Annotations as OA;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function verif(): ?Response
    {
        
        $isFullyAuthenticated = $this->get('security.authorization_checker')
        ->isGranted('ROLE_USER');

        if (!$isFullyAuthenticated) {
            // throw new AccessDeniedException();
            $response = $this->json('Unauthorized: Authentification requise', 401, [],[]);
            return $response;
        }
        
        return null;
    }

    /**
     * 
     * @OA\Tag(name="Utilisateur")
     * @OA\Tag(name="Client")
     * @OA\Get(
     *      summary="Liste des produits",
     * )
     * @OA\Response(
     *     response=200,
     *     description="Retourne la liste des produits",
     *)
     * @OA\Response(
     *     response=401,
     *     description="Unauthorized: Authentification requise",
     *)
     * @OA\Response(
     *     response=500,
     *     description="Retourne une erreur serveur",
     *)
     * @Route("/api/products", name="app_products", methods={"GET"})
     */
    public function showAllProduct(ProductsRepository $productsRepository): Response
    {
        // dd($this->verif());
        if(!$this->verif()) {
            $product = $productsRepository->findAll();
        
            $response = $this->json($product, 200, [],[]);
    
            return $response;
        }

        return $this->verif();
    }

    /**
     * 
     * @OA\Tag(name="Utilisateur")
     * @OA\Tag(name="Client")
     * @OA\Get(
     *      summary="Détail d'un produit",
     * )
     * 
     * @OA\Response(
     *     response=200,
     *     description="Retourne le détail d'un produit",
     *)
     * @OA\Response(
     *     response=401,
     *     description="Unauthorized: Authentification requise",
     *)
     * @OA\Response(
     *     response=404,
     *     description="Retourn Produit non trouvé, ou pas de route si l'Id n'est pas donné",
     *)
     * @Route("/api/product/{id}", name="app_product", methods={"GET"})
     */
    public function sowOneProduct($id, ProductsRepository $product): Response
    {
        if(!$this->verif()) {

            $product = $product->find($id);

            if ($product) {

                $response = $this->json($product, 200, [],[]);

                return $response;
            }

            
            $response = $this->json('Produit avec l\'Id:'.$id.' non trouvé', 404, [],[]);
            return $response;
        }

        return $this->verif();       
    }
}
