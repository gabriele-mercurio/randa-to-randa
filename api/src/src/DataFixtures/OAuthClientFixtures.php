<?php

namespace App\DataFixtures;

use App\Entity\OAuth2Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OAuthClientFixtures extends Fixture
{
    public const CLIENT_REFERENCE = 'oauth-client';

    public function load(ObjectManager $manager)
    {
        $client = new OAuth2Client();
        $client->setIdClient("Randa2RandaAppClient");
        $client->setSecret("FwPMFRlCa78GPQrO9zRWVRbjPCoPmaBQP254nx3g");
        
        $manager->persist($client);
        $manager->flush();
    }
}