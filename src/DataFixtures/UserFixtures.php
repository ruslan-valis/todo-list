<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserFixtures
 * @package App\DataFixtures
 * @author Ruslan Valis <ruslan.valis@itomy.ch>
 */
class UserFixtures extends Fixture
{
    public const ADMIN_USERNAME = 'admin';
    public const ADMIN_PASSWORD = 'admin';

    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername(self::ADMIN_USERNAME);
        $user->setPassword($this->userPasswordEncoder->encodePassword($user,self::ADMIN_PASSWORD));
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $manager->persist($user);

        $manager->flush();
    }
}
