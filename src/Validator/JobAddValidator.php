<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\HttpFoundation\Request;

class JobAddValidator
{
    public function getValidationErrors(Request $request): array
    {
        $errors = [];

        $title = $request->request->get('title');
        $text = $request->request->get('text');
        $dueTo = $request->request->get('due_to');
        $price = $request->request->get('price');
        $country = $request->request->get('country_id');
        $city = $request->request->get('city_id');
        $state = $request->request->get('state_id');

        if (strlen($title) < 3) {
            $errors[] = 'Pavadinimas per trumpas';
        }

        if (strlen($text) < 3) {
            $errors[] = 'Aprašymas per trumpas';
        }

        if (!$this->validateDate($dueTo)) {
            $errors[] = 'Įvesta data atlikimo data nėra validi';
        }

        if ($price < 0) {
            $errors[] = 'Neteisingai įvesta kaina';
        }

        if (filter_var($country, FILTER_VALIDATE_INT) === false || $country < 0) {
            $errors[] = 'Neteisingai pasirinkta šalis';
        }

        return $errors;
    }

    private function validateDate(string $date, string $format = 'Y-m-d')
    {
        try {
            $d = \DateTime::createFromFormat($format, $date);
            // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
            return $d && $d->format($format) === $date;
        } catch (\Throwable $exception) {
            return false;
        }
    }
}
