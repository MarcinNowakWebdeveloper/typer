<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260731201258 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add stage_view view and user_game table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE VIEW stage_view AS
select
    s.id,
    s.short_name,
    MIN(g.date) as start_date,
    MAX(g.date) as end_date
from stage as s
join stage_group as sg on sg.stage_id = s.id
join game as g on sg.id = g.stage_group_id
group by s.id');

        $this->addSql('CREATE TABLE user_game (id INT AUTO_INCREMENT NOT NULL, home_goals INT DEFAULT NULL, away_goals INT DEFAULT NULL, game_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_59AA7D45E48FD905 (game_id), INDEX IDX_59AA7D45A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE user_game ADD CONSTRAINT FK_59AA7D45E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_game ADD CONSTRAINT FK_59AA7D45A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_game DROP FOREIGN KEY FK_59AA7D45E48FD905');
        $this->addSql('ALTER TABLE user_game DROP FOREIGN KEY FK_59AA7D45A76ED395');
        $this->addSql('DROP TABLE user_game');
        $this->addSql('DROP VIEW stage_view');
    }
}
