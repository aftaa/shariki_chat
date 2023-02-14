<?php

namespace App\Manager;

use App\Repository\PushSubRepository;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\VAPID;
use Minishlink\WebPush\WebPush;

readonly class WebPushManager
{
    private array $auth;

    public function __construct(
        private PushSubRepository $pushSubRepository,
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
        $subs = $this->pushSubRepository->findAll();
        foreach ($subs as $sub) {
            echo $name = $sub->getName();
            $subscription = Subscription::create(json_decode($name, true));
            $push = new WebPush(['VAPID' => $this->auth]);
            $push->sendOneNotification($subscription, json_encode([
                'title' => 'Шарики-чат',
                'body' => $message,
            ]));
        }
    }
}