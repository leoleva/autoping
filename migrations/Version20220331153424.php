<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220331153424 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE address DROP INDEX UNIQ_D4E6F815D83CC1, ADD INDEX IDX_D4E6F815D83CC1 (state_id)');
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F81F92F3E70');
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F818BAC62AF');
        $this->addSql('DROP INDEX UNIQ_D4E6F81F92F3E70 ON address');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE address DROP INDEX IDX_D4E6F815D83CC1, ADD UNIQUE INDEX UNIQ_D4E6F815D83CC1 (state_id)');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F81F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F818BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D4E6F81F92F3E70 ON address (country_id)');
    }
}
