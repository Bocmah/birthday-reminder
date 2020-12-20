<?php

declare(strict_types=1);

namespace Vkbd\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201220102502 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create observers table';
    }

    public function up(Schema $schema) : void
    {
        $sql = <<<SQL
CREATE TABLE observers (
  id INT UNSIGNED AUTO_INCREMENT NOT NULL,
  first_name VARCHAR (255) NOT NULL,
  last_name VARCHAR(255) NOT NULL,
  vk_id BIGINT UNSIGNED UNIQUE NOT NULL,
  should_always_be_notified BOOL NOT NULL DEFAULT TRUE,
  PRIMARY KEY(id)
)
SQL;
        $this->addSql($sql);
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE observers');
    }
}
