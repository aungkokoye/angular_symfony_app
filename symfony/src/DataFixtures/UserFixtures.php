<?php

namespace App\DataFixtures;

use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::new()
            ->many(2)
            ->create([
                'roles' => ['ROLE_ADMIN'],
                'password' => UserFactory::ADMIN_PASSWORD
            ]);

        UserFactory::new()
            ->many(2)
            ->create([
                'roles' => ['ROLE_CLIENT'],
                'password' => UserFactory::CLIENT_PASSWORD
            ]);

        UserFactory::new()
            ->many(5)
            ->create();
    }

    public static function getGroups(): array
    {
        return ['users'];
    }
}
