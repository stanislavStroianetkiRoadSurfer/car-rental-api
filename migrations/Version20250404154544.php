<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250404154544 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Setting initial tables. (Cars, stations, bookings).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<'SQL'
            CREATE TABLE booking (id INT AUTO_INCREMENT NOT NULL, car_id INT NOT NULL, start_date DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', end_date DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', status VARCHAR(50) NOT NULL, customer_email VARCHAR(255) NOT NULL, INDEX IDX_E00CEDDEC3C6F69F (car_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL
        );
        $this->addSql(
            <<<'SQL'
            CREATE TABLE car (id INT AUTO_INCREMENT NOT NULL, station_id INT DEFAULT NULL, model VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, INDEX IDX_773DE69D21BDB235 (station_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL
        );
        $this->addSql(
            <<<'SQL'
            CREATE TABLE station (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, city VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL
        );
        $this->addSql(
            <<<'SQL'
            ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEC3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)
        SQL
        );
        $this->addSql(
            <<<'SQL'
            ALTER TABLE car ADD CONSTRAINT FK_773DE69D21BDB235 FOREIGN KEY (station_id) REFERENCES station (id)
        SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            <<<'SQL'
            ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEC3C6F69F
        SQL
        );
        $this->addSql(
            <<<'SQL'
            ALTER TABLE car DROP FOREIGN KEY FK_773DE69D21BDB235
        SQL
        );
        $this->addSql(
            <<<'SQL'
            DROP TABLE booking
        SQL
        );
        $this->addSql(
            <<<'SQL'
            DROP TABLE car
        SQL
        );
        $this->addSql(
            <<<'SQL'
            DROP TABLE station
        SQL
        );
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
