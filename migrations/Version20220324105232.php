<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220324105232 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $ltCountryId = 123;

        foreach ($this->getCities() as $name) {
            $this->addSql('insert into city (name, country_id) values ("'.$name.'", "'.$ltCountryId.'");');
        }

        $ltCountryId = 123;

        foreach ($this->getCities() as $name) {
            $this->addSql('insert into city (name, country_id) values ("'.$name.'", "'.$ltCountryId.'");');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }

    private function getCities(): array
    {
        return [
            'Vilnius',
            'Kaunas',
            'Klaipėda',
            'Šiauliai',
            'Panevėžys',
            'Akmenė',
            'Alytus',
            'Anykščiai',
            'Birštonas',
            'Biržai',
            'Druskininkai',
            'Elektrėnai',
            'Gargždai',
            'Ignalina',
            'Jonava',
            'Joniškis',
            'Jurbarkas',
            'Kaišiadorys',
            'Kalvarija',
            'KazlųRūda',
            'Kelmė',
            'Kėdainiai',
            'Kretinga',
            'Kupiškis',
            'Kuršėnai',
            'Lazdijai',
            'Marijampolė',
            'Mažeikiai',
            'Molėtai',
            'Neringa',
            'Pagėgiai',
            'Pakruojis',
            'Palanga',
            'Pasvalys',
            'Plungė',
            'Prienai',
            'Radviliškis',
            'Raseiniai',
            'Rietavas',
            'Rokiškis',
            'Skuodas',
            'Šakiai',
            'Šalčininkai',
            'Šilalė',
            'Šilutė',
            'Širvintos',
            'Švenčionys',
            'Tauragė',
            'Telšiai',
            'Trakai',
            'Ukmergė',
            'Utena',
            'Varėna',
            'Vievis',
            'Vilkaviškis',
            'Visaginas',
            'Zarasai'
        ];
    }
}
