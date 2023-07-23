<?php

namespace App\Service;

use App\Repository\PushSubRepository;
use App\Repository\ServerRepository;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

readonly class PushSubService
{
    private array $auth;

    public function __construct(
        private PushSubRepository $pushSubRepository,
        private ServerRepository $serverRepository,
    )
    {
        $this->auth = [
            'subject' => 'info@gelievyeshari24.ru',
            'publicKey' => file_get_contents(__DIR__ . '/../../etc/keys/public_key.txt'),
            'privateKey' => file_get_contents(__DIR__ . '/../../etc/keys/private_key.txt'),
        ];
    }

    /**
     * @throws \ErrorException
     */
    public function webPushSend(string $message): void
    {
        $server = $this->serverRepository->findOneBy(['active' => true]);

        $subs = $this->pushSubRepository->findAll();
        foreach ($subs as $sub) {
            $name = $sub->getName();
            $subscription = Subscription::create(json_decode($name, true));
            $push = new WebPush(['VAPID' => $this->auth]);
            $push->sendOneNotification($subscription, json_encode([
                'title' => 'Шарики-чат',
                'body' => $message,
                'url' => $server->getPushUrl(),
            ]));
        }
    }
}