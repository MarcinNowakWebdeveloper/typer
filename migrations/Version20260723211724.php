<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260723211724 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add team_group and team_group_team tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE team_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE team_group_team (group_id INT NOT NULL, team_id INT NOT NULL, INDEX IDX_D3638FAAFE54D947 (group_id), INDEX IDX_D3638FAA296CD8AE (team_id), PRIMARY KEY (group_id, team_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE team_group_team ADD CONSTRAINT FK_D3638FAAFE54D947 FOREIGN KEY (group_id) REFERENCES team_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team_group_team ADD CONSTRAINT FK_D3638FAA296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team_group_team DROP FOREIGN KEY FK_D3638FAAFE54D947');
        $this->addSql('ALTER TABLE team_group_team DROP FOREIGN KEY FK_D3638FAA296CD8AE');
        $this->addSql('DROP TABLE team_group');
        $this->addSql('DROP TABLE team_group_team');
    }
}
