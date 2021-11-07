<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211107215035 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE unavailable_person (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, planning_id INTEGER NOT NULL, person_id INTEGER NOT NULL, game INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_48BCB0033D865311 ON unavailable_person (planning_id)');
        $this->addSql('CREATE INDEX IDX_48BCB003217BBB47 ON unavailable_person (person_id)');
        $this->addSql('DROP INDEX IDX_54FDF319217BBB47');
        $this->addSql('DROP INDEX IDX_54FDF3193D865311');
        $this->addSql('CREATE TEMPORARY TABLE __temp__planning_person AS SELECT planning_id, person_id FROM planning_person');
        $this->addSql('DROP TABLE planning_person');
        $this->addSql('CREATE TABLE planning_person (planning_id INTEGER NOT NULL, person_id INTEGER NOT NULL, PRIMARY KEY(planning_id, person_id), CONSTRAINT FK_54FDF3193D865311 FOREIGN KEY (planning_id) REFERENCES planning (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_54FDF319217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO planning_person (planning_id, person_id) SELECT planning_id, person_id FROM __temp__planning_person');
        $this->addSql('DROP TABLE __temp__planning_person');
        $this->addSql('CREATE INDEX IDX_54FDF319217BBB47 ON planning_person (person_id)');
        $this->addSql('CREATE INDEX IDX_54FDF3193D865311 ON planning_person (planning_id)');
        $this->addSql('DROP INDEX IDX_573D1951DAADA679');
        $this->addSql('DROP INDEX IDX_573D19513D865311');
        $this->addSql('CREATE TEMPORARY TABLE __temp__planning_task_type AS SELECT planning_id, task_type_id FROM planning_task_type');
        $this->addSql('DROP TABLE planning_task_type');
        $this->addSql('CREATE TABLE planning_task_type (planning_id INTEGER NOT NULL, task_type_id INTEGER NOT NULL, PRIMARY KEY(planning_id, task_type_id), CONSTRAINT FK_573D19513D865311 FOREIGN KEY (planning_id) REFERENCES planning (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_573D1951DAADA679 FOREIGN KEY (task_type_id) REFERENCES task_type (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO planning_task_type (planning_id, task_type_id) SELECT planning_id, task_type_id FROM __temp__planning_task_type');
        $this->addSql('DROP TABLE __temp__planning_task_type');
        $this->addSql('CREATE INDEX IDX_573D1951DAADA679 ON planning_task_type (task_type_id)');
        $this->addSql('CREATE INDEX IDX_573D19513D865311 ON planning_task_type (planning_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE unavailable_person');
        $this->addSql('DROP INDEX IDX_54FDF3193D865311');
        $this->addSql('DROP INDEX IDX_54FDF319217BBB47');
        $this->addSql('CREATE TEMPORARY TABLE __temp__planning_person AS SELECT planning_id, person_id FROM planning_person');
        $this->addSql('DROP TABLE planning_person');
        $this->addSql('CREATE TABLE planning_person (planning_id INTEGER NOT NULL, person_id INTEGER NOT NULL, PRIMARY KEY(planning_id, person_id))');
        $this->addSql('INSERT INTO planning_person (planning_id, person_id) SELECT planning_id, person_id FROM __temp__planning_person');
        $this->addSql('DROP TABLE __temp__planning_person');
        $this->addSql('CREATE INDEX IDX_54FDF3193D865311 ON planning_person (planning_id)');
        $this->addSql('CREATE INDEX IDX_54FDF319217BBB47 ON planning_person (person_id)');
        $this->addSql('DROP INDEX IDX_573D19513D865311');
        $this->addSql('DROP INDEX IDX_573D1951DAADA679');
        $this->addSql('CREATE TEMPORARY TABLE __temp__planning_task_type AS SELECT planning_id, task_type_id FROM planning_task_type');
        $this->addSql('DROP TABLE planning_task_type');
        $this->addSql('CREATE TABLE planning_task_type (planning_id INTEGER NOT NULL, task_type_id INTEGER NOT NULL, PRIMARY KEY(planning_id, task_type_id))');
        $this->addSql('INSERT INTO planning_task_type (planning_id, task_type_id) SELECT planning_id, task_type_id FROM __temp__planning_task_type');
        $this->addSql('DROP TABLE __temp__planning_task_type');
        $this->addSql('CREATE INDEX IDX_573D19513D865311 ON planning_task_type (planning_id)');
        $this->addSql('CREATE INDEX IDX_573D1951DAADA679 ON planning_task_type (task_type_id)');
    }
}
