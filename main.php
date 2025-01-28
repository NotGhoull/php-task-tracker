<?php
require_once "taskManager.php";
require_once "magic.php";


// Create our task manager
$taskManager = new TaskManager();

// Ensure we have a action,
if (!isset($argv[1])) {
    printHelp();
    exit(0);
}

$action = $argv[1];

switch ($action) {
    case "add":
        $title = getArg($argv, 2, "Missing required parameter 'title' (task-cli add <title>)");

        // Add the task
        $id = $taskManager->addTask($title);

        // Assume okay. Program will crash on failure.
        print("Task added successfully (ID: {$id})");

        break;
    
    case "list":
        $filter = $argv[2] ?? null;

        $status = $statusMap[$filter] ?? null;
        $tasks = createFilteredTaskList($taskManager, $status);

        if (empty($tasks)) {
            echo $status === null ? "No tasks found." : "No tasks found for filter '{$filter}'";
            break;
        }

        foreach ($tasks as $task) {
            printTask($task, $taskManager);
        }

        break;
    
    // Fall through both of our cases
    case "mark-in-progress":
    case "mark-done":
        $id = getArg($argv, 2, "Missing required parameter 'id' (task-cli {$action} <id>)");

        // Set our status depending on the current action.
        $status = $action === "mark-in-progress" ? STATUS_IN_PROGRESS : STATUS_DONE;
        if (changeTaskStatus($id, $status, $taskManager)) {
            echo "Task updated successfully to " . ($status === STATUS_DONE ? "done" : "in progress") . "!";
            break;
        }

        // Fail state
        echo "Failed to update task. Does task {$id} exist?";

        break;
    
    case "update":
        $id = getArg($argv, 2, "Missing required parameter 'id' (task-cli update <id> <title>)");
        $title = getArg($argv, 3, "Missing required parameter 'title' (task-cli update <id> <title>)");

        $task = $taskManager->getTaskById($id);
        if (!$task) {
            echo "Task ID {$id} not found.";
            break;
        }

        $task["title"] = $title;
        $taskManager->updateTask($task);
        echo "Task updated successfully.";

        break;

    case "delete":
        $id = getArg($argv, 2, "Missing required parameter 'id' (task-cli delete <id>)");

        if ($taskManager->deleteTask($id)) {
            echo "Task deleted successfully.";
        } else {
            echo "Failed to delete task. Does task {$id} exist?";
        }
        break;
    
    case "help":
    default:
        printHelp();
        break;
}


function getArg(array $argv, int $index, string $errorMessage) {
    if (!isset($argv[$index])) {
        die($errorMessage . "\n");
    }
    
    return $argv[$index];
}

/**
 * A warpper function for updating tasks
 * @param int $id The task ID to update
 * @param int $newPriority The new priority of it
 * @param TaskManager $taskManager The task manager to use
 * @return bool If it succeeded
 */
function changeTaskStatus(int $id, int $newPriority, TaskManager $taskManager):bool {
    $task = $taskManager->getTaskById($id);
    if (!$task) {
        return false;
    }

    $task["status"] = $newPriority;
    return $taskManager->updateTask($task);
}

function createFilteredTaskList(TaskManager $taskManager, $status=null): array {
    $tasks = $taskManager->getAllTasks();
    
    if (empty($tasks)) {
        return [];
    }

    if (!isset($status)) {
        return $tasks;
    }

    $tasks = array_filter($tasks, fn($task) => $task["status"] === $status);
    return $tasks;
}

function printTask(array $task, TaskManager $taskManager) {
    // Create variables
    $relative_created = $taskManager->getRealtiveTime($task["createdAt"]);
    $relative_modified = $taskManager->getRealtiveTime($task["updatedAt"]);
    $status = $taskManager->convertStatusText($task["status"]);

    // Print
    echo "---------------\n";
    echo "{$task["title"]}\n";
    echo "[{$task["id"]}] - {$status}\n";
    echo "Created: {$relative_created} \n";
    echo "Updated: {$relative_modified} \n";
    echo "----------------\n";
    return;
}

function printHelp(): void {
    $help = <<<EOD

    Usage:
        task-cli [action]
    
    Actions:
        add <title>                     Add a new task with the given title.
        delete <id>                     Delete the task with the specified ID.
        update <id> <title>             Update the title of the task with the given ID.
        mark-in-progress <id>           Marks the task id as in progress (1)
        mark-done <id>                  Marks the task id as done (2)
        list [done|todo|in-progress]    If blank lists all tasks, otherwise, uses the given filter

    EOD;

    echo $help;

}