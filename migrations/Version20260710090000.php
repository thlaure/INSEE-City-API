<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260710090000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Narrows country_code to VARCHAR(2), matching ISO 3166-1 alpha-2 codes exactly.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cities ALTER COLUMN country_code TYPE VARCHAR(2)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cities ALTER COLUMN country_code TYPE VARCHAR(3)');
    }
}
