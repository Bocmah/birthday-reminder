<?php

declare(strict_types=1);

namespace BirthdayReminderMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201224192904 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create observees table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
            CREATE TABLE observees (
              first_name VARCHAR(255) NOT NULL,
              last_name VARCHAR(255) NOT NULL,
              vk_id BIGINT NOT NULL,
              birthdate DATE NOT NULL,
              observer_id BIGINT NOT NULL,
              PRIMARY KEY(observer_id, vk_id),
              FOREIGN KEY (observer_id) REFERENCES observers(vk_id) ON DELETE CASCADE
            )
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE observees');
    }
}
