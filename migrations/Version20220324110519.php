<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220324110519 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $lvCountryId = 226;

        foreach ($this->getStates() as $name) {
            $this->addSql('insert into state (name, country_id) values ("'.$name.'", "'.$lvCountryId.'");');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }

    private function getStates(): array
    {
        return [
            'California',
            'Arizona',
            'Texas',
            'Aliaska'
        ];
    }
}
