<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Book;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $encoder;

    /**
     * L'encodeur de mots de passe
     *
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        
        $content = '<p>' . join('</p><p>', $faker->paragraphs(10)) . '</p>';
        
        for ($i = 1; $i <= 10; $i++) {
            $book = new Book();

            $book->setTitle('title '.$i)
                ->setContent($content);

            $manager->persist($book);
        }


        for ($u = 1; $u <= 10; $u++) {
            $user = new User();

            $hash = $this->encoder->encodePassword($user, "password");

            $user->setFirstName($faker->firstName())
                 ->setLastName($faker->lastName)
                 ->setEmail($faker->email)
                 ->setPassword($hash);

            $manager->persist($user);
        }


        $manager->flush();
    }
}
