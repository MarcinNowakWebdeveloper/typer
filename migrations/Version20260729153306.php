<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260729153306 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add is_joker column to team table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team ADD is_joker TINYINT DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team DROP is_joker');
    }
}
