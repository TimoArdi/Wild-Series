<?php


namespace App\DataFixtures;

use App\Entity\Episode;
use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $slugify = new Slugify();
        $faker = Factory::create('en_US');
        for($i = 0; $i < 50; $i++) {
            $episode = new Episode();
            $episode->setSeason($this->getReference('season_' . rand(0,10)));
            $episode->setNumber(rand(1,10));
            $episode->setTitle($faker->word(rand(0, 2)));
            $episode->setSynopsis($faker->text(100));
            $slug = $slugify->generate($episode->getTitle());
            $episode->setSlug($slug);
            $manager->persist($episode);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return[SeasonFixtures::class];
    }
}
