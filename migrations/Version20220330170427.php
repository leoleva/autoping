<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220330170427 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE address CHANGE country_id country_id INT NOT NULL');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F81F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F818BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F815D83CC1 FOREIGN KEY (state_id) REFERENCES state (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D4E6F81F92F3E70 ON address (country_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D4E6F818BAC62AF ON address (city_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D4E6F815D83CC1 ON address (state_id)');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F8F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FBD8E0F8F5B7AF75 ON job (address_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F81F92F3E70');
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F818BAC62AF');
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F815D83CC1');
        $this->addSql('DROP INDEX UNIQ_D4E6F81F92F3E70 ON address');
        $this->addSql('DROP INDEX UNIQ_D4E6F818BAC62AF ON address');
        $this->addSql('DROP INDEX UNIQ_D4E6F815D83CC1 ON address');
        $this->addSql('ALTER TABLE address CHANGE country_id country_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE job DROP FOREIGN KEY FK_FBD8E0F8F5B7AF75');
        $this->addSql('DROP INDEX UNIQ_FBD8E0F8F5B7AF75 ON job');
    }
}
