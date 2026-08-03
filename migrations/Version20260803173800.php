<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Service\PointCountingStrategy\RelativeScoringStrategy;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260803173800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add relative scoring strategy';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('INSERT INTO point_counting_strategy (code, max_points_per_game, is_default) VALUES ("'.RelativeScoringStrategy::getCode().'", '.RelativeScoringStrategy::MAX_POINTS.', 0)');
    }

    public function down(Schema $schema): void
    {
    }
}
