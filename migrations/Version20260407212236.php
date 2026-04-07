<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260407212236 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cities (id UUID NOT NULL, insee_code VARCHAR(10) NOT NULL, name VARCHAR(255) NOT NULL, department_code VARCHAR(10) NOT NULL, region_code VARCHAR(10) NOT NULL, population INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D95DB16B15A3C1BC ON cities (insee_code)');
        $this->addSql('CREATE INDEX idx_cities_department_code ON cities (department_code)');
        $this->addSql('CREATE INDEX idx_cities_region_code ON cities (region_code)');
        $this->addSql('CREATE INDEX idx_cities_name ON cities (name)');
        $this->addSql('COMMENT ON COLUMN cities.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN cities.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN cities.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE cities');
    }
}
