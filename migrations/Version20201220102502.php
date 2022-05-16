<?php

declare(strict_types=1);

namespace VkbdMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201220102502 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create observers table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
            CREATE TABLE observers (
              vk_id BIGINT PRIMARY KEY,
              first_name VARCHAR(255) NOT NULL,
              last_name VARCHAR(255) NOT NULL,
              should_always_be_notified BOOLEAN NOT NULL DEFAULT TRUE
            )
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE observers');
    }
}
