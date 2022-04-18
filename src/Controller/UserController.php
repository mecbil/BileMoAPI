<?php

namespace App\Controller;

use OpenApi\Annotations as OA;
use App\Repository\ProductsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @OA\Response(
 *     response=401,
 *     description="Unauthorized: Authentification requise",
 *     @OA\JsonContent(default="Unauthorized: Authentification requise")
 * )
 */
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
     * @OA\Parameter(
     *     name="Page",
     *     in="query",
     *     description="Numéro de page",
     *     required=false,
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Retourne la liste des produits",
     *     @OA\JsonContent(ref="#/components/schemas/Produit")
     * )
     * @OA\Response(
     *     response=500,
     *     description="Erreur serveur",
     *     @OA\JsonContent(default="Erreur serveur")
     * )
     * @Route("/api/products", name="app_products", methods={"GET"})
     */
    public function showAllProduct(Request $request,PaginatorInterface $paginator, ProductsRepository $productsRepository, CacheInterface $cache): Response
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
            $itemParPager = 3;
            $currentPage = $request->query->getInt('Page', 1);
            $nbPages = ceil(count($products)/$itemParPager);

            $productPagine = $paginator->paginate(
                $products,
                $currentPage,
                $itemParPager
            );

            $response = $this->json(['Page No: '.$currentPage.' sur : '.$nbPages,'Liste des Produits', $productPagine], 200, [],['groups' => 'product:list']); 
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
     * @OA\Response(
     *     response=200,
     *     description="Retourne le détail d'un produit",
     *     @OA\JsonContent(ref="#/components/schemas/Produit_Detail")
     *)
     * @OA\Response(
     *     response=404,
     *     description="Retourn Produit non trouvé, ou pas de route si l'Id n'est pas donné",
     *     @OA\JsonContent(default="Exemple: Produit non trouvé")
     *)
     * @Route("/api/product/{id}", name="app_product", methods={"GET"})
     */
    public function sowOneProduct($id, ProductsRepository $productrepo, CacheInterface $cache): Response
    {
        if(!$this->verif()) {

            //Mise en cache des produits trouvés
            $product = $cache->get('userFind', function() use($productrepo, $id){
                return $productrepo->find($id);
            });

            if ($product) {
                // Envoi la liste des produits trouvé en json
                $response = $this->json($product, 200, [],['groups' => 'product:detail']);
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
