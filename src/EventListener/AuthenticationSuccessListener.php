<?php 

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use App\Entity\User;

class AuthenticationSuccessListener 
{
    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof User) {
            $event->setData([
                'status' => false,
                'message' => 'Wrong credentials!'
            ]);

            return;
        }
    
        $data = [
            'status' => true,
            'payload' => [
                'user' => [
                    'id' => $user->getId(),
                    'email' => $user->getUserIdentifier(),
                    'name' => $user->getName(),
                    'avatar' => $user->getAvatar(),
                ],
                'accessToken' => $data['token']
            ]
        ];
    
        $event->setData($data);
    }
}