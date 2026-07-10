<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260709160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds country_code, renames insee_code to local_code, and makes department_code/region_code nullable to support cities from countries other than France.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cities RENAME COLUMN insee_code TO local_code');
        $this->addSql("ALTER TABLE cities ADD COLUMN country_code VARCHAR(3) NOT NULL DEFAULT 'FR'");
        $this->addSql('ALTER TABLE cities ALTER COLUMN country_code DROP DEFAULT');
        $this->addSql('DROP INDEX UNIQ_D95DB16B15A3C1BC');
        $this->addSql('CREATE UNIQUE INDEX uniq_cities_country_code_local_code ON cities (country_code, local_code)');
        $this->addSql('CREATE INDEX idx_cities_country_code ON cities (country_code)');
        $this->addSql('ALTER TABLE cities ALTER COLUMN department_code DROP NOT NULL');
        $this->addSql('ALTER TABLE cities ALTER COLUMN region_code DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // Recreating NOT NULL is lossy if non-French rows with NULL department/region codes
        // exist by rollback time — same caveat as any nullable-to-required rollback.
        $this->addSql('ALTER TABLE cities ALTER COLUMN region_code SET NOT NULL');
        $this->addSql('ALTER TABLE cities ALTER COLUMN department_code SET NOT NULL');
        $this->addSql('DROP INDEX idx_cities_country_code');
        $this->addSql('DROP INDEX uniq_cities_country_code_local_code');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D95DB16B15A3C1BC ON cities (local_code)');
        $this->addSql('ALTER TABLE cities DROP COLUMN country_code');
        $this->addSql('ALTER TABLE cities RENAME COLUMN local_code TO insee_code');
    }
}
