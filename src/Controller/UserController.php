<?php

namespace App\Controller;

use OpenApi\Annotations as OA;
use App\Repository\ProductsRepository;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function showAllProduct(ProductsRepository $productsRepository, CacheInterface $cache): Response
    {
        if(!$this->verif()) {
            //Mise en cache des produits trouvés
            $products = $cache->get('usersFind', function() use($productsRepository){
                return $productsRepository->findAll();
            });
            if (!$products) {

                $response = $this->json('Aucun produit trouvé', 200, [],[]);
                return $response;
            }
            // envoi la liste des produits en json
            $response = $this->json($products, 200, [],[]);   
            return $response;
        }
        // Utilisateur non connecté
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
    public function sowOneProduct($id, ProductsRepository $productrepo, CacheInterface $cache): Response
    {
        if(!$this->verif()) {

            //Mise en cache des produits trouvés
            $product = $cache->get('usersFind', function() use($productrepo, $id){
                return $productrepo->find($id);
            });

            if ($product) {
                // Envoi la liste des produits trouvé en json
                $response = $this->json($product, 200, [],[]);
                return $response;
            }
            // Aucun produit trouvé            
            $response = $this->json('Produit avec l\'Id:'.$id.' non trouvé', 404, [],[]);
            return $response;
        }
        // Utilisateur non connecté
        return $this->verif();       
    }
}
