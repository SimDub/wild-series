<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use  Faker;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i=0; $i < 50; $i++) {
            $season = new Season();
            $faker = Faker\Factory::create('fr_FR');
            $season->setYear($faker->year);
            $season->setDescription($faker->sentence($nbWords = 6, $variableNbWords = true));
            $season->setProgram($this->getReference('program_'.rand(0, 5)));
            $manager->persist($season);
            $this->addReference('season_' . $i, $season);
        }

        $manager->flush();
    }
    public function getDependencies()

    {
        return [ProgramFixtures::class];
    }
}