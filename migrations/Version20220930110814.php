<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220930110814 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE forced_task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, task_type_id INTEGER NOT NULL, person_id INTEGER NOT NULL, game INTEGER NOT NULL, CONSTRAINT FK_F0A98C4CDAADA679 FOREIGN KEY (task_type_id) REFERENCES task_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F0A98C4C217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_F0A98C4CDAADA679 ON forced_task (task_type_id)');
        $this->addSql('CREATE INDEX IDX_F0A98C4C217BBB47 ON forced_task (person_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__unavailable_person AS SELECT id, planning_id, person_id, game FROM unavailable_person');
        $this->addSql('DROP TABLE unavailable_person');
        $this->addSql('CREATE TABLE unavailable_person (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, planning_id INTEGER NOT NULL, person_id INTEGER NOT NULL, game INTEGER NOT NULL, CONSTRAINT FK_48BCB0033D865311 FOREIGN KEY (planning_id) REFERENCES planning (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_48BCB003217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO unavailable_person (id, planning_id, person_id, game) SELECT id, planning_id, person_id, game FROM __temp__unavailable_person');
        $this->addSql('DROP TABLE __temp__unavailable_person');
        $this->addSql('CREATE INDEX IDX_48BCB003217BBB47 ON unavailable_person (person_id)');
        $this->addSql('CREATE INDEX IDX_48BCB0033D865311 ON unavailable_person (planning_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE forced_task');
        $this->addSql('CREATE TEMPORARY TABLE __temp__unavailable_person AS SELECT id, planning_id, person_id, game FROM unavailable_person');
        $this->addSql('DROP TABLE unavailable_person');
        $this->addSql('CREATE TABLE unavailable_person (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, planning_id INTEGER NOT NULL, person_id INTEGER NOT NULL, game INTEGER NOT NULL)');
        $this->addSql('INSERT INTO unavailable_person (id, planning_id, person_id, game) SELECT id, planning_id, person_id, game FROM __temp__unavailable_person');
        $this->addSql('DROP TABLE __temp__unavailable_person');
        $this->addSql('CREATE INDEX IDX_48BCB0033D865311 ON unavailable_person (planning_id)');
        $this->addSql('CREATE INDEX IDX_48BCB003217BBB47 ON unavailable_person (person_id)');
    }
}
