<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260408150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Makes postal_code nullable and converts empty strings to NULL.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE cities SET postal_code = NULL WHERE postal_code = ''");
        $this->addSql('ALTER TABLE cities ALTER COLUMN postal_code DROP NOT NULL');
        $this->addSql('ALTER TABLE cities ALTER COLUMN postal_code DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql("UPDATE cities SET postal_code = '' WHERE postal_code IS NULL");
        $this->addSql('ALTER TABLE cities ALTER COLUMN postal_code SET NOT NULL');
        $this->addSql("ALTER TABLE cities ALTER COLUMN postal_code SET DEFAULT ''");
    }
}
