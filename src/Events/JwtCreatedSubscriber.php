<?php

namespace App\Events;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JwtCreatedSubscriber {
    public function updateJwtData(JWTCreatedEvent $event){
        // 1. Récupérer l'utilisateur pour avoir son firstname et son lastname
        $user = $event->getUser();

        // 2. Enrichir les data pour qu'elles contiennent les données de l'utilisateur
        $data = $event->getData();

        $data['firstName'] = $user->getFirstName();
        $data['lastName'] = $user->getLastName();

        $event->setData($data);

        // dd($event->getData());
    }
}