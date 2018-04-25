<?php
namespace Tests\Unit\Solean\CleanProspecter\Factory;

use Solean\CleanProspecter\Entity\User;

class UserFactory
{
    /**
     * User with credential login/password/salt/ROLE/language
     */
    public static function regular() : User
    {
        $user     = new User();

        $user->setId(123);
        $user->setUserName('login');
        $user->setSalt('salt');
        $user->setPassword(md5(sprintf('%s%s', 'password', $user->getSalt())));
        $user->addRole('ROLE');
        $user->setLanguage('FR');
        $user->setOrganization(OrganizationFactory::creator());

        return $user;
    }
}
