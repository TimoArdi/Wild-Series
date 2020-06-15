<?php

namespace App\DataFixtures;

use App\Service\Slugify;
use Faker;
use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    const ACTORS = [
        ['name' => 'Andrew Lincoln', 'program' => ['program_0', 'program_5']],
        ['name' => 'Norman Reedus',  'program' => ['program_0']],
        ['name' => 'Lauren Cohan',  'program' => ['program_0']],
        ['name' => 'Danae Gurira',  'program' => ['program_0']],
    ];

    public function load(ObjectManager $manager)
    {
        $slugify = new Slugify();
       foreach (self::ACTORS as $key => $actorData) {
           $actor = new Actor();
           $actor->setName($actorData['name']);
           foreach ($actorData['program'] as $programs) {
               $actor->addProgram($this->getReference($programs));
           }
           $slug = $slugify->generate($actor->getName());
           $actor->setSlug($slug);
           $manager->persist($actor);
           $this->addReference('actor_' . $actorData['name'], $actor);
       }
            $faker = Faker\Factory::create('en_US');
            for ($i = 5; $i < 51; $i++) {
                $actor = new Actor();
                $actor->setName($faker->name());
                $actor->addProgram($this->getReference('program_' . rand(0,5)));
                $slug = $slugify->generate($actor->getName());
                $actor->setSlug($slug);
                $manager->persist($actor);
            }

        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }
}
