<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260728123735 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add game table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, home_goals INT DEFAULT NULL, away_goals INT DEFAULT NULL, stage_group_id INT NOT NULL, home_team_id INT NOT NULL, away_team_id INT NOT NULL, INDEX IDX_232B318CF6B31D2A (stage_group_id), INDEX IDX_232B318C9C4C13F6 (home_team_id), INDEX IDX_232B318C45185D02 (away_team_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CF6B31D2A FOREIGN KEY (stage_group_id) REFERENCES stage_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C9C4C13F6 FOREIGN KEY (home_team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C45185D02 FOREIGN KEY (away_team_id) REFERENCES team (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CF6B31D2A');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C9C4C13F6');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C45185D02');
        $this->addSql('DROP TABLE game');
    }
}
