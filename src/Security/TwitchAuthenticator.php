<?php

namespace App\Security;

use App\Entity\User;
use App\Security\Event\SocialRegistrationEvent;
use Depotwarehouse\OAuth2\Client\Twitch\Entity\TwitchUser;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\ExpiredException;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class TwitchAuthenticator extends OAuth2Authenticator
{
    /**
     * @var ClientRegistry
     */
    protected $clientRegistry;
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct(
        ClientRegistry $clientRegistry,
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'app_check_twitch';
    }

    public function authenticate(Request $request): PassportInterface
    {
        $session = $request->getSession();
        $client = $this->clientRegistry->getClient('twitch');

        /** @var AccessToken $accessToken */
        $accessToken = $session->get('access_token');

        if (! $accessToken || ! isset($accessToken->getValues()['id_token'])) {
            $accessToken = $this->fetchAccessToken($client);
        }

        if ($accessToken->hasExpired()) {
            $accessToken = $client->refreshAccessToken($accessToken->getRefreshToken());
        }
        // This is currently not working, the id_token gets lost most of the time
        // $session->set('access_token', $accessToken);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client, $session) {
                $newUser = false;

                try {
                    /** @var TwitchUser $twitchUser */
                    $twitchUser = $client->fetchUserFromToken($accessToken)->toArray();
                } catch (ExpiredException $exception) {
                    $accessToken = $client->refreshAccessToken($accessToken->getRefreshToken());
                    // $session->set('access_token', $accessToken);
                    $twitchUser = $client->fetchUserFromToken($accessToken)->toArray();
                }
                $entityRepository = $this->entityManager->getRepository(User::class);

                $existingUser = $entityRepository
                    ->findOneBy(['twitchId' => $twitchUser['sub']]);

                if ($existingUser) {
                    return $existingUser;
                }

                if (! $twitchUser['email_verified']) {
                    throw new \Exception('Only validated emails are allowed');
                }

                $email = $twitchUser['email'];
                $user = $entityRepository->findOneBy(['email' => $email]);

                if (! $user) {
                    $user = new User();
                    $user
                        ->setEmail($email)
                        ->setUsername($twitchUser['preferred_username']);

                    $newUser = true;
                }

                $user->setTwitchId($twitchUser['sub']);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                if ($newUser) {
                    $this->eventDispatcher->dispatch(new SocialRegistrationEvent($user));
                }

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $targetUrl = $this->router->generate('dashboard');

        return new RedirectResponse($targetUrl);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }
}
