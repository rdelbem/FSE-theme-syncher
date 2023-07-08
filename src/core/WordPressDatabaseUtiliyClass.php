<?php

namespace FseThemeSyncher\core;

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
    protected $tableName;

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
     * @return string | null
     */
    public function insert(array $data): string|null
    {
        global $wpdb;
        if (empty($data)) {
            return null;
        }

        try {
            $wpdb->insert($this->tableName, $data);
            return 'Success, data was inserted' . $wpdb->insert_id;
        } catch (\Throwable $th) {
            return 'Error: ' . $th;
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
     * @return bool|int|\Throwable
     */
    public function update(array $data, array $conditionValue): bool|int|\Throwable
    {
        global $wpdb;

        if (empty($data)) {
            return false;
        }

        try {
            $updated = $wpdb->update($this->tableName, $data, $conditionValue);
            return $updated;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    /**
     * Delete row on the database table
     *
     * @param  array $conditionValue - Key value pair for the where clause of the query
     *
     * @return bool|int - Num rows deleted
     */
    public function delete(array $conditionValue): bool|int
    {
        if (empty($conditionValue)) {
            return false;
        }

        global $wpdb;

        $deleted = $wpdb->delete($this->tableName, $conditionValue);

        return $deleted;
    }
}