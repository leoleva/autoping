<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\User;
use App\Enum\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('/register', name: 'app_register', methods: 'GET|POST')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $responseParams = [];

        $errors = $this->getFormValidationErrors($request);

        if ($request->getMethod() === 'POST') {
            if (count($errors) === 0) {
                $address = new Address();

                if ($request->request->getInt('city') !== 0) {
                    $address->setCityId($request->request->get('city'));
                }
                if ($request->request->getInt('state') !== 0) {
                    $address->setStateId($request->request->get('state'));
                }
                $address->setCountryId($request->request->get('country'));

                $entityManager->persist($address);
                $entityManager->flush();

                $user = new User();
                $user->setUserType(UserType::from($request->request->get('type')));
                $user->setEmail($request->request->get('email'));
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $request->get('password')
                    )
                );
                $user->setAddressId($address->getId());
                $user->setCreatedAt(new \DateTime());

                $entityManager->persist($user);
                $entityManager->flush();

                $responseParams['registration_successful'] = true;
            } else {
                $responseParams['registration_errors'] = $errors;
            }
        }

        return $this->render('registration/register.html.twig', $responseParams);
    }

    private function getFormValidationErrors(Request $request): array
    {
        $errors = [];

        if ($request->getMethod() !== 'POST') {
            return $errors;
        }

        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $country = $request->request->getInt('country');
        $state = $request->request->getInt('state');
        $city = $request->request->getInt('city');
        $type = $request->request->get('type');


        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = 'Blogas el.paštas';
        }

        if (count($this->userRepository->findBy(['email' => $email])) > 0) {
            $errors[] = 'Vartotojas jau egzistuoja';
        }

        if (!is_string($password)) {
            $errors[] = 'Slaptažodį turi sudaryti simboliai';
        } else {
            if (strlen($password) < 3) {
                $errors[] = 'Slaptažodis per trumpas';
            }
        }

        if ($country === 0) {
            $errors[] = 'Nepasirinkta šalis';
        }

        if (!in_array($type, [UserType::Buyer->value, UserType::Specialist->value], true)) {
            $errors[] = 'Vartotojo tipas neegzistuoja';
        }

        return $errors;
    }
}
