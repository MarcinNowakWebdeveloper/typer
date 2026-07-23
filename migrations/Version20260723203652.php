<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260723203652 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add file and team tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, origin_name VARCHAR(255) NOT NULL, mime_type VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, logo_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_C4E0A61FF98F144A (logo_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61FF98F144A FOREIGN KEY (logo_id) REFERENCES file (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61FF98F144A');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE team');
    }
}
