<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211024213912 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE person (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE planning (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, game_count INTEGER NOT NULL)');
        $this->addSql('CREATE TABLE planning_person (planning_id INTEGER NOT NULL, person_id INTEGER NOT NULL, PRIMARY KEY(planning_id, person_id))');
        $this->addSql('CREATE INDEX IDX_54FDF3193D865311 ON planning_person (planning_id)');
        $this->addSql('CREATE INDEX IDX_54FDF319217BBB47 ON planning_person (person_id)');
        $this->addSql('CREATE TABLE planning_task_type (planning_id INTEGER NOT NULL, task_type_id INTEGER NOT NULL, PRIMARY KEY(planning_id, task_type_id))');
        $this->addSql('CREATE INDEX IDX_573D19513D865311 ON planning_task_type (planning_id)');
        $this->addSql('CREATE INDEX IDX_573D1951DAADA679 ON planning_task_type (task_type_id)');
        $this->addSql('CREATE TABLE task_type (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
    }

    public function down(Schema $schema): void
    {

        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE planning');
        $this->addSql('DROP TABLE planning_person');
        $this->addSql('DROP TABLE planning_task_type');
        $this->addSql('DROP TABLE task_type');
    }
}
