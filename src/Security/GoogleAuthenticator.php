<?php

namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Users; // your user entity
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class GoogleAuthenticator extends OAuth2Authenticator
{
    private $clientRegistry;
    private $entityManager;
    private $router;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google');
        $accessToken = $this->fetchAccessToken($client);

        

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($accessToken, $client) {
                /** @var GoogleUser $GoogleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);
                $email = $googleUser->getEmail();
                
                // 1) have they logged in with Google before? Easy!
                $existingUser = $this->entityManager->getRepository(Users::class)->findOneBy(['email' => $email]);

                if ($existingUser) {
                    return $existingUser;
                }

        //         // 2) do we have a matching user by email?
        //         $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        //         // 3) Maybe you just want to "register" them by creating
        //         // a User object
        //         $user->setGoogleId($googleUser->getId());
        //         $this->entityManager->persist($user);
        //         $this->entityManager->flush();

        //         return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // change "app_homepage" to some route in your app
        $targetUrl = $this->router->generate('connecte');

        return new RedirectResponse($targetUrl);
    
        // $response = $this->json('Identification failure', 404, [],[]);

        // return $response;
        // or, on success, let the request continue to be handled by the controller
        // return null;

        // if ($request->isXmlHttpRequest()) {
            // $response = new Response();
            // $content = array('success' => true, 'message' => 'User Identified', 'Code' => '200');
            // $data = json_encode($content);
            // $response->headers->set('Content-Type', 'application/json');
            // $response->setContent($data);
            // return $response;
            
            // $response = new Response(json_encode($content) , 200 , array( 'Content-Type' => 'application/json' ));
            // return $response;
        // }
        
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
        // $response = $this->json('Identification failure', 404, [],[]);

        // $response = new Response();
        // $content = array('success:' => false, 'Message' => 'User NOT found', 'Code error' => '404');
        // $data = json_encode($content);
        // $response->headers->set('Content-Type', 'application/json');
        // $response->setContent($data);
        // return $response;
    }
}