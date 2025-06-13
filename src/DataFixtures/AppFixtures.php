<?php

namespace App\DataFixtures;

use App\Entity\DevMetalGroup;
use App\Entity\DevMetalSong;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;


    public function __construct(){
        $this->faker = Factory::create("fr_FR");
    }

    public function load(ObjectManager $manager): void
    {
        $devMetalGroups = [];
        for($i = 0; $i < 10; ++$i) {
            $devMetalGroup = new DevMetalGroup();
            $devMetalGroup->setName($this->faker->name());
            $manager->persist($devMetalGroup);
            $devMetalGroups[] = $devMetalGroup;
        }

        for($i = 0; $i < 100; ++$i) {
            $devMetalSong = new DevMetalSong();
            $devMetalSong->setName($this->faker->text(32));
            $devMetalSong->setAuthor($devMetalGroups[array_rand($devMetalGroups)]);
            $manager->persist($devMetalSong);
        }


        $manager->flush();
    }
}
