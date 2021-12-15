<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class TwitchCallbackController extends AbstractController
{
    /**
     * @Route("/connect/twitch", name="app_connect_twitch")
     *
     * @param ClientRegistry $clientRegistry
     *
     * @return RedirectResponse
     * @throws \JsonException
     */
    public function connectTwitch(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry
            ->getClient('twitch')
            ->redirect([
                'openid',
                'moderation:read',
                'user:read:email',
            ], [
                // @TODO: Use serializer
                'claims' => \json_encode([
                    'userinfo' => [
                        'picture' => null,
                    ],
                    'id_token' => [
                        'email' => null,
                        'email_verified' => null,
                        'preferred_username' => null,
                    ],
                ], JSON_THROW_ON_ERROR),
            ]);
    }

    /**
     * @Route("/check/twitch", name="app_check_twitch")
     */
    public function checkTwitch()
    {
    }
}
