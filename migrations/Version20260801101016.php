<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Service\PointCountingStrategy\FixedPointsScoringStrategy;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260801101016 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add point_counting_strategy and user_game_points tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE point_counting_strategy (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(100) NOT NULL, max_points_per_game DOUBLE PRECISION NOT NULL, is_default TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_game_points (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, points DOUBLE PRECISION NOT NULL, user_game_id INT NOT NULL, point_counting_strategy_id INT NOT NULL, INDEX IDX_F394EA74BC82C70F (user_game_id), INDEX IDX_F394EA74CE92A43A (point_counting_strategy_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE user_game_points ADD CONSTRAINT FK_F394EA74BC82C70F FOREIGN KEY (user_game_id) REFERENCES user_game (id)');
        $this->addSql('ALTER TABLE user_game_points ADD CONSTRAINT FK_F394EA74CE92A43A FOREIGN KEY (point_counting_strategy_id) REFERENCES point_counting_strategy (id)');
        $this->addSql('INSERT INTO point_counting_strategy (code, max_points_per_game, is_default) VALUES ("'.FixedPointsScoringStrategy::getCode().'", '.FixedPointsScoringStrategy::MAX_POINTS.', 1)');

        $this->addSql('ALTER TABLE user_game DROP points');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_game_points DROP FOREIGN KEY FK_F394EA74BC82C70F');
        $this->addSql('ALTER TABLE user_game_points DROP FOREIGN KEY FK_F394EA74CE92A43A');
        $this->addSql('DROP TABLE point_counting_strategy');
        $this->addSql('DROP TABLE user_game_points');
        $this->addSql('ALTER TABLE user_game ADD points INT DEFAULT NULL');
    }
}
