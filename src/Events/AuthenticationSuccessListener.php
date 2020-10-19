<?php

    namespace App\Events;
    use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

    use Symfony\Component\Security\Core\User\UserInterface;

    class AuthenticationSuccessListener{
    
        /**
         * @param AuthenticationSuccessEvent $event
         * @return void
         */
        public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
        {
            $data = $event->getData();
            $user = $event->getUser();
            if (!$user instanceof UserInterface) {
                return;
            }
        
        
            $data['user'] = array(
                'id' => $user->getId(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                // 'roles' => $user->getRoles()
            );
        
            $event->setData($data);
        }
    }
?>