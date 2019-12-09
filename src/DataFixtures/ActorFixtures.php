<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use  Faker;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    const ACTORS = [
        'Norman Reedus',
        'Danai Gurira',
        'Christian Serratos',
        'Khary Payton',
        'Jean Coucou',
        'Pierre Toto',
    ];
    public function load(ObjectManager $manager)
    {
        foreach (self::ACTORS as $key => $actorName){
            $actor = new Actor();
            $actor->setName($actorName);
            $actor->addProgram($this->getReference('program_'.rand(0, 5)));
            $manager->persist($actor);
            $this->addReference('actor_' . $key, $actor);
        }
        for ($i=0; $i < 50; $i++) {
            $actor = new Actor();
            $faker = Faker\Factory::create('fr_FR');
            $actor->setName($faker->name);
            $actor->addProgram($this->getReference('program_'.rand(0, 5)));
            $manager->persist($actor);

        }

        $manager->flush();
    }
    public function getDependencies()

    {
        return [ProgramFixtures::class];
    }
}