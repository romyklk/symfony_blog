<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Address;
use App\Entity\Article;
use App\Entity\Profile;
use App\Entity\Category;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class BlogFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR'); // creation d'un objet Faker

        $users = [];

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setFullName($faker->name)
                ->setEmail($faker->email)
                ->setPhone($faker->phoneNumber)
                ->setPassword($faker->password)
                ->setCreatedAt($faker->dateTimeBetween('-5 year', 'now'));

            $address = new Address();

            $address->setStreet($faker->streetName)
                ->setZipCode($faker->postcode)
                ->setCity($faker->city)
                ->setCountry($faker->country)
                ->setCreatedAt($faker->dateTimeBetween('-5 year', 'now'))
                ->setUser($user);

            $profile = new Profile();

            $profile->setPicture($faker->imageUrl())
                ->setDescription($faker->text(500))
                ->setDateBirth($faker->dateTimeBetween('-30 years', '-18 years'))
                ->setCoverImage($faker->imageUrl())
                ->setCreatedAt($faker->dateTimeBetween('-5 year', 'now'))
                ->setUser($user);

            $manager->persist($profile);

            $manager->persist($address);

            $manager->persist($user);

            $users[] = $user;

        }

        $categories = [];

        $slugify = new Slugify(); // creation d'un objet Slugify pour créer un slug à partir du titre de l'article

        for ($i = 0; $i < 5; $i++) {
            $category = new Category();
            $category->setName($faker->word)
                ->setDescription($faker->text(100))
                ->setImageUrl($faker->imageUrl())
                ->setSlug($slugify->slugify($category->getName()))
                ->setCreatedAt($faker->dateTimeBetween('-5 year', 'now'));

            $manager->persist($category);

            $categories[] = $category;
        }

        for ($i = 0; $i < 150; $i++) {
            $article = new Article();

            
            
            $article->setTitle($faker->sentence())
                ->setContent($faker->text(2500))
/*                 ->setCreatedAt($faker->dateTimeBetween('-1 week', 'now')) */
                ->setImageUrl($faker->imageUrl())
                ->setAuthor($faker->randomElement($users))
                ->setCreatedAt($faker->dateTimeBetween('-5 week', 'now'))
                ->setSlug($slugify->slugify($article->getTitle()))
                ->addCategory($faker->randomElement($categories));

            $manager->persist($article);
        }

        $manager->flush();
    }
}
