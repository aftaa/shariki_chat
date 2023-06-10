<?php

namespace App\Controller;

use App\Entity\PushSub;
use App\Repository\PushSubRepository;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\VAPID;
use Minishlink\WebPush\WebPush;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/web-notification')]
class WebNotificationController extends AbstractController
{
    #[Route('/', name: 'app_web_notification_index')]
    public function index(): Response
    {
        return $this->render('web_notification/index.html.twig');
    }

    #[Route('/index1', name: 'app_web_notification_index1')]
    public function index1(): Response
    {
        return $this->render('web_notification/index1.html.twig');
    }

    /**
     * @return void
     * @throws \ErrorException
     */
    #[Route('/send-push-notification', name: 'app_webnotification_sendpushnotification')]
    public function sendPushNotification(): Response
    {
        $subscription = Subscription::create(json_decode(file_get_contents('php://input'), true));
        $auth = array(
            'VAPID' => array(
                'subject' => 'http://localhost/',
                'publicKey' => file_get_contents(__DIR__ . '/../../etc/keys/public_key.txt'),
                'privateKey' => file_get_contents(__DIR__ . '/../../etc/keys/private_key.txt'),
            ),
        );

        $webPush = new WebPush($auth);

        $report = $webPush->sendOneNotification(
            $subscription,
            "Hello! 👋"
        );

        // handle eventual errors here, and remove the subscription from your server if it is expired
        $endpoint = $report->getRequest()->getUri()->__toString();

        if ($report->isSuccess()) {
            echo "[v] Message sent successfully for subscription {$endpoint}.";
        } else {
            echo "[x] Message failed to sent for subscription {$endpoint}: {$report->getReason()}";
        }

        return new Response('');
    }

    #[Route('/push-subscription', name: 'app_webnotification_pushsubscription')]
    public function pushSubscription(): Response
    {
        $subscription = json_decode(file_get_contents('php://input'), true);

        if (!isset($subscription['endpoint'])) {
            echo 'Error: not a subscription';
            return new Response('');
        }

        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'POST':
                // create a new subscription entry in your database (endpoint is unique)
                break;
            case 'PUT':
                // update the key and token of subscription corresponding to the endpoint
                break;
            case 'DELETE':
                // delete the subscription corresponding to the endpoint
                break;
            default:
                echo "Error: method not handled";
                return new Response('');
        }
        return new Response('');
    }

    #[Route('/push1', name: 'app_webnotification_push1')]
    public function push1(Request $request, PushSubRepository $pushSubRepository): Response
    {
// (B) GET SUBSCRIPTION

        $sub = $request->get('sub');
        $pushSub = $pushSubRepository->findBy(['name' => $sub]);
        if (!$pushSub) {
            $pushSub = new PushSub();
            $pushSub->setName($sub);
            $pushSubRepository->save($pushSub, true);
        }
        return new Response('');
    }

    #[Route('/vapid', name: 'app_webnotification_createkeys')]
    public function createKeys(): Response
    {
        print_r(VAPID::createVapidKeys());
        return new Response('');
    }

    /**
     * @return void
     * @throws \ErrorException
     */
    public function addToDb(): void
    {
        $sub = Subscription::create(json_decode($_REQUEST["sub"], true));
        $message = $_REQUEST['message'];

// (C) NEW WEB PUSH OBJECT - CHANGE TO YOUR OWN!
        $push = new WebPush(["VAPID" => [
            "subject" => "https://xn--80a0bn.xn--24-6kchemaby3a4d4erbe.xn--p1ai/mobile",
            'publicKey' => file_get_contents(__DIR__ . '/../../etc/keys/public_key.txt'),
            'privateKey' => file_get_contents(__DIR__ . '/../../etc/keys/private_key.txt'),
        ]]);

// (D) SEND TEST PUSH NOTIFICATION
        $result = $push->sendOneNotification($sub, json_encode([
            "title" => "Шарики-чат",
            "body" => $message,
//            "icon" => "i-loud.png",
//            "image" => "i-cover.png"
        ]));
        $endpoint = $result->getRequest()->getUri()->__toString();

// (E) HANDLE RESULT - OPTIONAL
        if ($result->isSuccess()) {
            // echo "Successfully sent {$endpoint}.";
        } else {
            // echo "Send failed {$endpoint}: {$result->getReason()}";
            // $result->getRequest();
            // $result->getResponse();
            // $result->isSubscriptionExpired();
        }
    }
}
