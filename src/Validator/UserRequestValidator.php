<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\User;
use App\Enum\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class UserRequestValidator
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    public function validateCreationRequest(Request $request): array
    {
        $errors = [];

        if ($request->getMethod() !== 'POST') {
            return $errors;
        }

        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $country = $request->request->getInt('country_id');
        $state = $request->request->getInt('state_id');
        $city = $request->request->getInt('city_id');
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

    public function validateUpdateRequest(Request $request, UserInterface $user): array
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        $errors = [];

        if ($user->getEmail() !== $email && count($this->userRepository->findBy(['email' => $email])) > 0) {
            $errors[] = 'Vartotojas jau egzistuoja';
        }

        if (!is_string($password)) {
            $errors[] = 'Blogas slaptažodis';
        }

        if ($password !== '' && ($password) < 3) {
            $errors[] = 'Slaptažodis per trumpas';
        }

        return $errors;
    }
}
