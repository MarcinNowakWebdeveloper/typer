<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260728123608 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add stage and stage_group tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE stage (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, short_name VARCHAR(15) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE stage_group (id INT AUTO_INCREMENT NOT NULL, group_id INT DEFAULT NULL, stage_id INT NOT NULL, INDEX IDX_ED67F601FE54D947 (group_id), INDEX IDX_ED67F6012298D193 (stage_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE stage_group ADD CONSTRAINT FK_ED67F601FE54D947 FOREIGN KEY (group_id) REFERENCES team_group (id)');
        $this->addSql('ALTER TABLE stage_group ADD CONSTRAINT FK_ED67F6012298D193 FOREIGN KEY (stage_id) REFERENCES stage (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE stage_group DROP FOREIGN KEY FK_ED67F601FE54D947');
        $this->addSql('ALTER TABLE stage_group DROP FOREIGN KEY FK_ED67F6012298D193');
        $this->addSql('DROP TABLE stage');
        $this->addSql('DROP TABLE stage_group');
    }
}
