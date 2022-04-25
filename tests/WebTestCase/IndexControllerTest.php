<?php

declare(strict_types=1);

namespace Tests\WebTestCase;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('p', 'Šios platformos tikslas padėti klientams užsakyti, o automobilio specialistas atlikti, automobilio apžiūras');
        self::assertSelectorTextContains('a[class="btn btn-primary btn-lg"]', 'Užsiregistruokite');
    }
}