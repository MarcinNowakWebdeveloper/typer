<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260730115352 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add joker table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE joker (id INT AUTO_INCREMENT NOT NULL, points INT DEFAULT NULL, user_id INT NOT NULL, team_id INT NOT NULL, UNIQUE INDEX UNIQ_94E6D497A76ED395 (user_id), INDEX IDX_94E6D497296CD8AE (team_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE joker ADD CONSTRAINT FK_94E6D497A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE joker ADD CONSTRAINT FK_94E6D497296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE joker DROP FOREIGN KEY FK_94E6D497A76ED395');
        $this->addSql('ALTER TABLE joker DROP FOREIGN KEY FK_94E6D497296CD8AE');
        $this->addSql('DROP TABLE joker');
    }
}
