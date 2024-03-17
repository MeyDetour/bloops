<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager ): void
    {
        $faker = Factory::create();

        $categoryList = ['Animaux','AnimauxTropsMignons','Chats','POLITIQUE','VOYAGE'];
        for($i = 0 ; $i< count($categoryList)  ; $i++){
            $category = new Category();
           $category->setName($categoryList[$i]);
            $manager->persist($category);
        }
        $user = new User();
        $user->setEmail('meydetour@gmail.com');
        $user->setPassword('$2y$13$1XA.j4pbJQ/0tGCOSYOjpOkXJM9tUKFt8Kts2wGBPhwCgLqiKjk72' );
        $user->setRoles(['ROLE_ADMIN']);
        $user->setUsername('ADMIN1');
        $user->setIsVerified(true);
        $manager->persist($user);
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
