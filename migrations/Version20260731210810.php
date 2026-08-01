<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260731210810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add points column to user_game table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_game ADD points INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_game DROP points');
    }
}
