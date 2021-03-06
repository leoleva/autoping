<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_USER")
 * @Security("is_granted('ROLE_USER')")
 */
abstract class  AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    protected function getUser(): ?User
    {
        if (parent::getUser() === null) {
            return null;
        }

        return parent::getUser(); // TODO: Change the autogenerated stub
    }
}