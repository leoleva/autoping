<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220331152131 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job_offer DROP FOREIGN KEY FK_288A3A4EBE04EA9');
        $this->addSql('DROP INDEX FK_288A3A4EBE04EA9 ON job_offer');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job_offer ADD CONSTRAINT FK_288A3A4EBE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
        $this->addSql('CREATE INDEX FK_288A3A4EBE04EA9 ON job_offer (job_id)');
    }
}
