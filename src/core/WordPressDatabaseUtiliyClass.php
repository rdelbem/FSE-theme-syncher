<?php

namespace FseThemeSyncher\core;

use \WP_Error;

/**
 * Utility class, an abstraction around WP database
 * functions and data handling.
 */
abstract class WordPressDatabaseUtiliyClass
{
    /**
     * The current table name
     *
     * @var string
     */
    public $tableName;

    /**
     * Constructor for the database class to inject the table name
     *
     * @param string $tableName - The current table name
     */
    public function __construct($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * Insert data into the current data
     *
     * @param  array $data - Data to enter into the database table
     *
     * @return array | null
     */
    public function insert(array $data): array|null
    {
        global $wpdb;
        if (empty($data)) {
            return null;
        }

        try {
            $wpdb->insert($this->tableName, $data);
            return [
                'error' => null,
                'record' => $wpdb->insert_id
            ];
        } catch (\Throwable $th) {
            return [
                'error' => $th->getMessage(),
                'record' => null
            ];
        }
    }

    /**
     * Get all from the selected table
     *
     * @param  string | null $orderBy - Order by column name
     *
     * @return array | object | null
     */
    public function getAll(string|null $orderBy = null): array|object|null
    {
        global $wpdb;

        $sql = 'SELECT * FROM `' . $this->tableName . '`';

        if (!empty($orderBy)) {
            $sql .= ' ORDER BY ' . $orderBy;
        }

        return $wpdb->get_results($sql);
    }

    /**
     * Get a value by a condition
     *
     * @param  array  $conditionValue - A key value pair of the conditions you want to search on
     * @param  string $condition - A string value for the condition of the query default to equals
     *
     * @return array
     */
    public function getBy(array $conditionValue, $condition = '='): array
    {
        global $wpdb;

        $sql = 'SELECT * FROM `' . $this->tableName . '` WHERE ';

        foreach ($conditionValue as $field => $value) {
            switch (strtolower($condition)) {
                case 'in':
                    if (!is_array($value)) {
                        throw new \Exception('Values for IN query must be an array.', 1);
                    }

                    $sql .= $wpdb->prepare('`%s` IN (%s)', $field, implode(',', $value));
                    break;

                default:
                    $sql .= $wpdb->prepare('`' . $field . '` ' . $condition . ' %s', $value);
                    break;
            }
        }

        return $wpdb->get_results($sql);
    }

    /**
     * Update a table record in the database
     *
     * @param  array $data           - Array of data to be updated
     * @param  array $conditionValue - Key value pair for the where clause of the query
     *
     * @return bool|int|WP_Error
     */
    public function update(array $data, array $conditionValue): bool|int|WP_Error
    {
        if (empty($data)) {
            return false;
        }

        global $wpdb;

        $updated = $wpdb->update($this->tableName, $data, $conditionValue);
        if($updated < 0) {
            return new WP_Error('update-from-fse-theme-synch', 'It was not possible to update db.', $data);
        }
        return $updated;
    }

    /**
     * Delete row on the database table
     *
     * @param  array $conditionValue - Key value pair for the where clause of the query
     *
     * @return bool|int|WP_Error - Num rows deleted or an instance of WP_Error
     */
    public function delete(array $conditionValue): bool|int|WP_Error
    {
        if (empty($conditionValue)) {
            return false;
        }

        global $wpdb;

        $deleted = $wpdb->delete($this->tableName, $conditionValue);
        if($deleted < 0){
            return new WP_Error('delete-from-fse-theme-synch', 'It was not possible to delete from db.', $conditionValue);
        }
        return $deleted;
    }
}