<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211023200756 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, planning_id INTEGER NOT NULL, datetime DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , team1 VARCHAR(255) NOT NULL, team2 VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_232B318C3D865311 ON game (planning_id)');
        $this->addSql('CREATE TABLE person (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE planning (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE planning_person (planning_id INTEGER NOT NULL, person_id INTEGER NOT NULL, PRIMARY KEY(planning_id, person_id))');
        $this->addSql('CREATE INDEX IDX_54FDF3193D865311 ON planning_person (planning_id)');
        $this->addSql('CREATE INDEX IDX_54FDF319217BBB47 ON planning_person (person_id)');
        $this->addSql('CREATE TABLE task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, assignee_id INTEGER DEFAULT NULL, type_id INTEGER NOT NULL, planning_id INTEGER NOT NULL, label VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_527EDB2559EC7D60 ON task (assignee_id)');
        $this->addSql('CREATE INDEX IDX_527EDB25C54C8C93 ON task (type_id)');
        $this->addSql('CREATE INDEX IDX_527EDB253D865311 ON task (planning_id)');
        $this->addSql('CREATE TABLE task_type (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, planning_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_FF6DC3523D865311 ON task_type (planning_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE planning');
        $this->addSql('DROP TABLE planning_person');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE task_type');
    }
}
