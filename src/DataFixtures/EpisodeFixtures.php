<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use  Faker;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i=1; $i < 51; $i++) {
            $season = new Episode();
            $faker = Faker\Factory::create('fr_FR');
            $season->setTitle($faker->text(22));
            $season->setNumber($i);
            $season->setSeason($this->getReference('season_'.rand(0, 49)));
            $season->setSynopsis($faker->text(100));
            $manager->persist($season);
        }

        $manager->flush();
    }
    public function getDependencies()

    {
        return [SeasonFixtures::class];
    }
}