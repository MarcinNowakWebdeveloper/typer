<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260804090215 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add point_counting_strategy_translation table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE point_counting_strategy_translation (id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(5) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, point_counting_strategy_id INT DEFAULT NULL, INDEX IDX_B469456FCE92A43A (point_counting_strategy_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE point_counting_strategy_translation ADD CONSTRAINT FK_B469456FCE92A43A FOREIGN KEY (point_counting_strategy_id) REFERENCES point_counting_strategy (id)');

        $htmlForClassic = <<<'HTML'
<section class="scoring-strategy">
    <p>
        W tej strategii każdy typ trafienia ma z góry określoną wartość punktową. Za wyjątkiem dokłądnego trafienia, punkty się sumują.
        Za jedno spotkanie można zdobyć maksymalnie <strong>12 punktów</strong>.
    </p>

    <table class="table table-striped">
    <thead>
    <tr>
        <th>Typ trafienia</th>
        <th class="text-end">Punkty</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>🎯 Dokładnie trafiony wynik</td>
        <td class="text-end"><strong>12 pkt</strong></td>
    </tr>
    <tr>
        <td>🏆 Trafiony zwycięzca lub remis</td>
        <td class="text-end"><strong>+5 pkt</strong></td>
    </tr>
    <tr>
        <td>⚽ Trafiona łączna liczba bramek w meczu</td>
        <td class="text-end"><strong>+3 pkt</strong></td>
    </tr>
    <tr>
        <td>➕ Jeden z wyników różni się od rzeczywistego o dokładnie 1 bramkę</td>
        <td class="text-end"><strong>+1 pkt</strong></td>
    </tr>
    </tbody>
    </table>

    <h3>Przykłady</h3>

    <ul>
        <li><strong>Typ:</strong> 2:1, <strong>wynik:</strong> 2:1 → <strong>12 pkt</strong></li>
        <li><strong>Typ:</strong> 3:1, <strong>wynik:</strong> 2:1 → <strong>6 pkt</strong> (5 + 1)</li>
        <li><strong>Typ:</strong> 1:2, <strong>wynik:</strong> 2:1 → <strong>3 pkt</strong> (trafiona liczba bramek)</li>
        <li><strong>Typ:</strong> 3:2, <strong>wynik:</strong> 4:1 → <strong>8 pkt</strong> (5+3)</li>
        <li><strong>Typ:</strong> 2:2, <strong>wynik:</strong> 2:1 → <strong>1 pkt</strong> (różnica jednej bramki)</li>
    </ul>
</section>
HTML;
        $this->addSql(
            'INSERT INTO point_counting_strategy_translation
        (locale, name, point_counting_strategy_id, description)
     VALUES (:locale, :name, :strategy, :description)',
            [
                'locale' => 'pl',
                'name' => 'Klasyczna',
                'strategy' => 1,
                'description' => $htmlForClassic,
            ]
        );

        $htmlForRelative = <<<'HTML'
<section class="scoring-strategy">
    <p>
        W tej strategii uczestnicy rywalizują między sobą.
        Liczy się nie tylko poprawne wytypowanie zwycięzcy lub remisu, ale również
        to, jak blisko rzeczywistego wyniku był Twój typ.
        Za jedno spotkanie można zdobyć maksymalnie <strong>15 punktów</strong>.
    </p>

    <h3>Punktacja podstawowa</h3>

    <table class="table table-striped">
        <thead>
        <tr>
            <th>Typ trafienia</th>
            <th class="text-end">Punkty</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>🎯 Dokładnie trafiony wynik</td>
            <td class="text-end"><strong>15 pkt</strong></td>
        </tr>
        <tr>
            <td>🏆 Trafiony zwycięzca lub remis</td>
            <td class="text-end"><strong>3 pkt</strong></td>
        </tr>
        <tr>
            <td>➕ Jeden z wyników różni się od rzeczywistego o dokładnie 1 bramkę</td>
            <td class="text-end"><strong>1 pkt</strong></td>
        </tr>
        </tbody>
    </table>

    <h3>Dodatkowe punkty</h3>

    <p>
        Spośród wszystkich osób, które nie trafiły dokładnego wyniku, ale trafiły zwycięzcę/remis,
        przyznawane są dodatkowe punkty za typy najbardziej zbliżone do rzeczywistego rezultatu.
    </p>

    <h4>⚽ Najbliżej sumy bramek / 📊 Najbliżej różnicy bramek</h4>

    <table class="table table-striped">
        <thead>
        <tr>
            <th>Miejsce</th>
            <th class="text-end">Punkty</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>1.</td>
            <td class="text-end"><strong>3 pkt</strong></td>
        </tr>
        <tr>
            <td>2.</td>
            <td class="text-end"><strong>2 pkt</strong></td>
        </tr>
        <tr>
            <td>3.</td>
            <td class="text-end"><strong>1 pkt</strong></td>
        </tr>
        </tbody>
    </table>

    <div class="alert alert-info mt-3">
        <strong>Uwaga:</strong> Jeżeli kilka osób zajmie to samo miejsce,
        wszystkie otrzymują taką samą liczbę punktów.
    </div>

    <h3>Przykład</h3>

    <p><strong>Rzeczywisty wynik:</strong> 4:2</p>

    <table class="table table-striped">
        <thead>
        <tr>
            <th>Osoba</th>
            <th>Typ</th>
            <th>Punktacja</th>
            <th>Suma</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Andrzej</td>
            <td>4:2</td>
            <td>15 pkt</td>
            <td>15 pkt</td>
        </tr>
        <tr>
            <td>Michał</td>
            <td>5:2</td>
            <td>3 pkt (zwycięzca) + 3 za najbliższa suma bramek + 2 vice za najbliższa różnicę bramek + 1 różnica jednej bramki</td>
            <td>9 pkt</td>
        </tr>
        <tr>
            <td>Maciej</td>
            <td>3:1</td>
            <td>3 pkt (zwycięzca) + 2 za vice najbliższa suma bramek + 3 za najbliższa różnicę bramek</td>
            <td>8 pkt</td>
        </tr>
        <tr>
            <td>Agata</td>
            <td>2:0</td>
            <td>3 pkt (zwycięzca) + 3 za najbliższa różnicę bramek</td>
            <td>6 pkt</td>
        </tr>
        <tr>
            <td>Marek</td>
            <td>5:0</td>
            <td>3 pkt (zwycięzca) + 3 za najbliższa suma bramek + 1 za najbliższa różnicę bramek</td>
            <td>7 pkt</td>
        </tr>
        <tr>
            <td>Zuza</td>
            <td>4:1</td>
            <td>3 pkt (zwycięzca) + 3 za najbliższa suma bramek  + 2 za vice najbliższa różnicę bramek + 1 różnica jednej bramki</td>
            <td>9 pkt</td>
        </tr>
        <tr>
            <th>Bonifacy</th>
            <td>2:1</td>
            <td>3 pkt (zwycięzca) + 1 za najbliższa suma bramek  + 2 za vice najbliższa różnicę bramek</td>
            <td>6 pkt</td>
        </tr>
        </tbody>
    </table>
</section>
HTML;

        $this->addSql(
            'INSERT INTO point_counting_strategy_translation
        (locale, name, point_counting_strategy_id, description)
     VALUES (:locale, :name, :strategy, :description)',
            [
                'locale' => 'pl',
                'name' => 'Proporcjonalna',
                'strategy' => 2,
                'description' => $htmlForRelative,
            ]
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE point_counting_strategy_translation DROP FOREIGN KEY FK_B469456FCE92A43A');
        $this->addSql('DROP TABLE point_counting_strategy_translation');
    }
}
