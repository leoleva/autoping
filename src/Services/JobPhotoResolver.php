<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\JobPhotoDTO;
use Symfony\Component\HttpFoundation\Request;

class JobPhotoResolver
{
    /**
     * @param Request $request
     * @return JobPhotoDTO[]
     */
    public static function resolveFromRequest(Request $request): array
    {
        $jobPhotoDTOs = [];

        echo '<pre>';

        foreach ($request->request->get('text') as $item => $value) {
            var_dump($item, $value);
            exit;
        }

        return $jobPhotoDTOs;
    }
}
