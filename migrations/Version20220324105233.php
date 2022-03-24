<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220324105233 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $lvCountryId = 117;

        foreach ($this->getCities() as $name) {
            $this->addSql('insert into city (name, country_id) values ("'.$name.'", "'.$lvCountryId.'");');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }

    private function getCities(): array
    {
        return [
            'Saldus',
            'Ryga',
            'Broceni',
        ];
    }
}
