<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220930111037 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__forced_task AS SELECT id, task_type_id, person_id, game FROM forced_task');
        $this->addSql('DROP TABLE forced_task');
        $this->addSql('CREATE TABLE forced_task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, task_type_id INTEGER NOT NULL, person_id INTEGER NOT NULL, planning_id INTEGER NOT NULL, game INTEGER NOT NULL, CONSTRAINT FK_F0A98C4CDAADA679 FOREIGN KEY (task_type_id) REFERENCES task_type (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F0A98C4C217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F0A98C4C3D865311 FOREIGN KEY (planning_id) REFERENCES planning (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO forced_task (id, task_type_id, person_id, game) SELECT id, task_type_id, person_id, game FROM __temp__forced_task');
        $this->addSql('DROP TABLE __temp__forced_task');
        $this->addSql('CREATE INDEX IDX_F0A98C4C217BBB47 ON forced_task (person_id)');
        $this->addSql('CREATE INDEX IDX_F0A98C4CDAADA679 ON forced_task (task_type_id)');
        $this->addSql('CREATE INDEX IDX_F0A98C4C3D865311 ON forced_task (planning_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__forced_task AS SELECT id, task_type_id, person_id, game FROM forced_task');
        $this->addSql('DROP TABLE forced_task');
        $this->addSql('CREATE TABLE forced_task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, task_type_id INTEGER NOT NULL, person_id INTEGER NOT NULL, game INTEGER NOT NULL, CONSTRAINT FK_F0A98C4CDAADA679 FOREIGN KEY (task_type_id) REFERENCES task_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F0A98C4C217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO forced_task (id, task_type_id, person_id, game) SELECT id, task_type_id, person_id, game FROM __temp__forced_task');
        $this->addSql('DROP TABLE __temp__forced_task');
        $this->addSql('CREATE INDEX IDX_F0A98C4CDAADA679 ON forced_task (task_type_id)');
        $this->addSql('CREATE INDEX IDX_F0A98C4C217BBB47 ON forced_task (person_id)');
    }
}
