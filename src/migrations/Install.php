<?php
/**
 * Trustpilot plugin for Craft CMS 3.x
 *
 * Interact with Trustpilot APIs
 *
 * @link      https://scaramanga.agency
 * @copyright Copyright (c) 2021 Scaramanga Agency
 */

namespace scaramangagency\trustpilot\migrations;

use scaramangagency\trustpilot\Trustpilot;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * @author    Scaramanga Agency
 * @package   Trustpilot
 * @since     1.0.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================
    public $driver;

    // Public Methods
    // =========================================================================
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        return $this->createTables();
    }

    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================
    protected function createTables()
    {
        $tablesCreated = false;

        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%trustpilot_settings}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable('{{%trustpilot_settings}}', [
                'id' => $this->primaryKey(),
                'siteId' => $this->integer()->null(),
                'apiKey' => $this->text(),
                'apiSecret' => $this->text(),
                'trustpilotUsername' => $this->text(),
                'trustpilotPassword' => $this->text(),
                'invitationSenderEmail' => $this->text(),
                'invitationSenderName' => $this->text(),
                'invitationReplyToEmail' => $this->text(),
                'trustpilotUrl' => $this->text(),
                'accessToken' => $this->text(),
                'refreshToken' => $this->text(),
                'businessUnitId' => $this->text(),
                'currentTrustpilotUrl' => $this->text(),
                'expiresIn' => $this->text(),
                'createdTimestamp' => $this->dateTime(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid()
            ]);
            $this->addForeignKey(
                $this->db->getForeignKeyName('{{%trustpilot_settings}}', 'siteId'),
                '{{%trustpilot_settings}}',
                'siteId',
                '{{%sites}}',
                'id',
                'CASCADE',
                'CASCADE'
            );
        }

        return $tablesCreated;
    }

    protected function removeTables()
    {
        $this->dropTableIfExists('{{%trustpilot_settings}}');
    }
}
