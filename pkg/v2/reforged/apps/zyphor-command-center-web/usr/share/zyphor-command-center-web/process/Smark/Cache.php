<?php

namespace App\Smark;

// $cache = new Cache('file');

// // CREATE
// $cache->create('cache', [
//     'id'   => 1,
//     'name' => 'Mark Jason',
// ]);

// $cache->create('cache', [
//     'id'   => 2,
//     'name' => 'Mark Jason 2',
// ]);

// // READ ALL
// foreach ($cache->all('cache') as $person) {
//     $this->info($person['name']);
// }

// // READ ONE
// $user = $cache->find('cache', 1);
// $this->info('Found: ' . $user['name']);

// // UPDATE
// $cache->update('cache', 1, [
//     'name' => 'Mark Jason Updated',
// ]);

// // DELETE
// $cache->delete('cache', 2);

// Cache class to handle caching using different storage mechanisms
class Cache {
    protected $storage;

    protected function generateId()
    {
        return uniqid('', true);
    }

    protected function now()
    {
        return date('Y-m-d H:i:s');
    }

    public function __construct($storage = 'file')
    {
        if ($storage === 'file') {
            $this->storage = new FileCache();
        } elseif ($storage === 'memory') {
            $this->storage = new MemoryCache();
        }
    }

    /* =======================
     | BASIC METHODS
     ======================= */

    public function set($key, $value)
    {
        $this->storage->set($key, $value);
    }

    public function get($key)
    {
        return $this->storage->get($key);
    }

    /* =======================
     | CRUD METHODS
     ======================= */

    // CREATE
    public function create($key, array $item)
    {
        $data = $this->get($key) ?? [];

        $item['id'] = $item['id'] ?? $this->generateId();
        $item['created_at'] = $this->now();
        $item['updated_at'] = $this->now();

        $data[] = $item;
        $this->set($key, $data);

        return $item;
    }

    // READ ALL
    public function all($key)
    {
        return $this->get($key) ?? [];
    }

    // READ ONE
    public function find($key, $id)
    {
        foreach ($this->all($key) as $item) {
            if (($item['id'] ?? null) == $id) {
                return $item;
            }
        }
        return null;
    }

    // UPDATE
    public function update($key, $id, array $newData)
    {
        $data = $this->all($key);

        foreach ($data as $index => $item) {
            if (($item['id'] ?? null) == $id) {
                $data[$index] = array_merge(
                    $item,
                    $newData,
                    ['updated_at' => $this->now()]
                );

                $this->set($key, $data);
                return $data[$index];
            }
        }

        return null;
    }

    // DELETE
    public function delete($key, $id)
    {
        $data = array_filter($this->all($key), function ($item) use ($id) {
            return ($item['id'] ?? null) != $id;
        });

        $this->set($key, array_values($data));
        return true;
    }

    // CLEAR CACHE KEY
    public function clear($key)
    {
        $this->set($key, []);
    }
}

// FileCache class for file-based caching implementation
class FileCache {
    // Method to store a value in the cache file
    public function set($key, $value) {
        // Serialize the value and write it to a file named after the key
        file_put_contents("cache/{$key}.cache", serialize($value));
    }

    // Method to retrieve a value from the cache file
    public function get($key) {
        // Construct the filename for the cache file
        $file = "cache/{$key}.cache";
        // Check if the cache file exists
        return file_exists($file) ?
            // If it exists, read the file, unserialize the content, and return it
            unserialize(file_get_contents($file)) :
            // If it doesn't exist, return null
            null;
    }
}

// MemoryCache class for in-memory caching implementation
class MemoryCache {
    // Protected array to hold cached data in memory
    protected $data = [];

    // Method to store a value in memory using a specific key
    public function set($key, $value) {
        // Store the value in the data array with the specified key
        $this->data[$key] = $value;
    }

    // Method to retrieve a value from memory using a specific key
    public function get($key) {
        // Return the value associated with the key if it exists; otherwise, return null
        return $this->data[$key] ?? null;
    }
}
