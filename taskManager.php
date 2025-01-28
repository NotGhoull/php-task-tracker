<?php

require_once "magic.php";

/**
 * Manages tasks
 */
class TaskManager {
    /**
     * The name of the file where the data is stored.
     * @var string
     */
    private const DATABASE_FILE = "data.json";
    /**
     * The statuses the tasks can be in
     * @var array
     */
    // private const STATUS_MAP = [
    //     0 => "Todo",
    //     1 => "In progress",
    //     2 => "Done",
    // ];

    /**
     * Ensures that the database exists
     * @throws RuntimeException If the database cannot be created for whatever reason
     * @return void
     */
    private function ensureDatabaseExists(): void {
        if (!file_exists(self::DATABASE_FILE)) {
            if (file_put_contents(self::DATABASE_FILE,"[]") === false) {
                throw new RuntimeException("Failed to create database.");
            }
        }
    }

    /**
     * Reads the database and returns the result
     * @return array Returns all data or nothing.
     */
    private function readDatabase(): array {
        $this->ensureDatabaseExists();
        $content = file_get_contents(self::DATABASE_FILE);
        return json_decode($content, true) ?? [];
    }

    /**
     * Writes data into the database
     * @param array $data The data to write
     * @throws RuntimeException If the data can't be written
     * @return void
     */
    private function writeDatabase(array $data): void {
        $this->ensureDatabaseExists();
        if (file_put_contents(self::DATABASE_FILE, json_encode($data)) === false) {
            throw new RuntimeException("Failed to write to database.");
        }
    }

    /**
     * Appends a task onto the end of the database
     * @param string $title The title of the task
     * @return int The ID of the task
     */
    public function addTask(string $title): int {
        $tasks = $this->readDatabase();
        $newId = count($tasks);
        
        // Create a new task object
        $tasks[] = [
            "id" => $newId,
            "title" => $title,
            "status" => 0,
            "createdAt" => time(),
            "updatedAt" => time(),
        ];

        $this->writeDatabase($tasks);
        return $newId;
    }

    /**
     * Gets all the tasks in the database
     * @return array All the tasks
     */
    public function getAllTasks(): array {
        return $this->readDatabase();
    }

    /**
     * Gets a task from the given ID
     * @param int $id The ID to search for
     * @return array|null If the ID is found, returns the object otherwise null
     */
    public function getTaskById(int $id): ?array {
        $tasks = $this->readDatabase();
        return $tasks[$id] ?? null;
    }

    /**
     * Updates a task object from its ID with the new one.
     * @param array $task The new task
     * @return bool If it worked or not
     */
    public function updateTask(array $task): bool {
        $tasks = $this->readDatabase();
        if (!isset($tasks[$task['id']])) {
            return false;
        }

        $task['updatedAt'] = time();
        $tasks[$task['id']] = $task;
        $this->writeDatabase($tasks);
        return true;
    }

    /**
     * Deletes a task with a given ID from the database
     * @param int $id Task to delete
     * @return bool If it worked or not
     */
    public function deleteTask(int $id): bool {
        $tasks = $this->readDatabase();
        if (!isset($tasks[$id])) {
            return false;
        }
        
        unset($tasks[$id]);
        $this->writeDatabase($tasks);
        return true;
    }

    /**
     * Converts the int status into text
     * @param int $status The value to swap
     * @return string The text value
     */
    public function convertStatusText(int $status): string {
        return STATUS_MAP[$status] ?? "Unknown";
    }

    /**
     * Converts Unix time into relative time
     * @param int $timestamp The unix timestamp to switch
     * @return string The formatted string
     */
    public function getRealtiveTime(int $timestamp): string {
        $diff = time() - $timestamp;

        $intervals = [
            ['year', 31536000],
            ['month', 2592000],
            ['week', 604800],
            ['day', 86400],
            ['hour', 3600],
            ['minute', 60],
            ['second', 1]
        ];

        if ($diff < 5) {
            return "Just now";
        }


        foreach ($intervals as [$unit, $seconds]) {
            $count = floor($diff / $seconds);
            if ($count > 0) {
                return $count . " " . $unit . ($count == 1 ? "" : "s") . " ago";
            }
        }


        return "Just now";
    }
}