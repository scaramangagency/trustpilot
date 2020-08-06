<?php
/**
 * Trustpilot plugin for Craft CMS 3.x
 *
 * Interact with Trustpilot APIs
 *
 * @link      https://scaramanga.agency
 * @copyright Copyright (c) 2020 Scaramanga Agency
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
class Install extends Migration {
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function safeUp() {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        return $this->createTables();
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @return bool
     */
    protected function createTables() {
        $tablesCreated = false;

        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%trustpilot_settings}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%trustpilot_settings}}',
                [
                    'id'                    => $this->primaryKey(),
                    'accessToken'           => $this->text(),
                    'refreshToken'          => $this->text(),
                    'businessUnitId'        => $this->text(),
                    'currentTrustpilotUrl'  => $this->text(),
                    'expiresIn'             => $this->text(),
                    'createdTimestamp'      => $this->dateTime()->notNull(),
                    'dateCreated'           => $this->dateTime()->notNull(),
                    'dateUpdated'           => $this->dateTime()->notNull(),
                    'uid'                   => $this->uid()
                ]
            );
        }

        return $tablesCreated;
    }

    /**
     * @return void
     */
    protected function removeTables() {
        $this->dropTableIfExists('{{%trustpilot_settings}}');
    }
}