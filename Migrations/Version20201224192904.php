<?php

declare(strict_types=1);

namespace Vkbd\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201224192904 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create observees table';
    }

    public function up(Schema $schema) : void
    {
        $sql = <<<SQL
CREATE TABLE observees (
  id INT UNSIGNED AUTO_INCREMENT NOT NULL,
  first_name VARCHAR (255) NOT NULL,
  last_name VARCHAR(255) NOT NULL,
  vk_id BIGINT UNSIGNED UNIQUE NOT NULL,
  birthdate DATE NOT NULL,
  observer_id INT UNSIGNED NOT NULL,
  PRIMARY KEY(id),
  FOREIGN KEY (observer_id) REFERENCES observers(id) ON DELETE CASCADE
)
SQL;
        $this->addSql($sql);
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE observees');
    }
}
