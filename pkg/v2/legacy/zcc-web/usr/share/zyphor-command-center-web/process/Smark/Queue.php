<?php

namespace App\Smark;

/**
 * push($task)
 * run()
 */

class Queue
{
    // Protected array to hold the queued tasks
    protected static $queue = [];

    // Method to add a task to the queue
    public static function push($task) {
        // Append the provided task to the end of the queue array
        self::$queue[] = $task;
    }

    // Method to execute all tasks in the queue
    public static function run() {
        // Loop through each task in the queue
        foreach (self::$queue as $task) {
            // Call the task as a callable (function or method)
            call_user_func($task);
        }
    }
}
