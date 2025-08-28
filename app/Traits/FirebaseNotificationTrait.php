<?php

namespace App\Traits;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;

trait FirebaseNotificationTrait
{
    protected $messaging;

    public function initializeFirebase()
    {
        if (!$this->messaging) {
            $firebase = (new Factory)
                ->withServiceAccount(config('services.firebase.credentials.file'));

            $this->messaging = $firebase->createMessaging();
        }
    }

    /**
     * Sending message to a single User
     * @param mixed $request
     * @param mixed $token
     * @return array
     */
    public function unicast($request, $token)
    {
        $this->initializeFirebase();

        try {
            $notification = Notification::create($request->title, $request->body);

            $message = CloudMessage::withTarget('token', $token)
                ->withNotification($notification);

            $this->messaging->send($message);

            return ['success' => true, 'message' => 'Notification sent successfully'];
        } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
            Log::warning("FCM token not found: $token");
            return ['success' => false, 'message' => 'Token not found'];
        } catch (\Kreait\Firebase\Exception\Messaging\InvalidArgument $e) {
            Log::warning("Invalid FCM token: $token");
            return ['success' => false, 'message' => 'Invalid token'];
        } catch (\Kreait\Firebase\Exception\MessagingException $e) {
            Log::error('Firebase Messaging Error: ' . $e->getMessage());
            return ['success' => false, 'message' => "FCM Messaging failed :{{$e->getMessage()}}"];
        } catch (\Exception $e) {
            Log::error('Unexpected Firebase Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Unexpected error'];
        }
    }


    public function subscribeToTopic($deviceToken, $topic)
    {
        $this->initializeFirebase();

        try {
            $this->messaging->subscribeToTopic($topic, $deviceToken);
            Log::info("Subscribed to topic: $topic with device token: $deviceToken");
            return ['success' => true, 'message' => 'Subscribed to topic successfully!'];
        } catch (\Exception $e) {
            Log::error('Firebase Topic Subscription Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to subscribe to topic'];
        }
    }


    public function unsubscribeFromTopic($deviceToken, $topic)
    {
        $this->initializeFirebase();

        try {
            $this->messaging->unsubscribeFromTopic($topic, $deviceToken);
            Log::info('Unsubscribed from topic: ' . $topic);
            return ['success' => true];
        } catch (\Exception $e) {
            Log::error('Unsubscribe error: ' . $e->getMessage());
            return ['success' => false];
        }
    }


    public function sendNotificationToTopic($topic, $title, $body)
    {
        if (empty($topic)) {
            Log::error('Topic is empty');
            return;
        }
        $this->initializeFirebase();


        try {
            //$notification = Notification::create($title, $body);
            $message = CloudMessage::new()->toTopic('topic')
                ->withNotification([
                    'title' => $title,
                    'body' => $body,
                ]);

            $this->messaging->send($message);
            Log::info('Notification sent successfully to topic: ' . $topic);
            return ['success' => true, 'message' => 'Notification sent successfully!'];
        } catch (\Exception $e) {
            Log::error('Firebase Topic Notification Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to send notification'];
        }
    }
}
