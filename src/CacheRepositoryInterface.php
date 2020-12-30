<?php

namespace Skaffold\Cache;

interface CacheRepositoryInterface
{
    /**
     * Deletes the specified key from the repository.
     * 
     * @param  string  $key  The key to be deleted.
     * @return void
     */
    public function delete(string $key): void;

    /**
     * Fills the repository with the passed items.
     * 
     * @return void
     */
    public function fill(array $items): void;

    /**
     * Empties the repository of all data.
     * 
     * @return void
     */
    public function flush(): void;

    /**
     * Returns the value of the specified key or default if not found.
     * 
     * @param  string  $key  The key to be returned.
     * @param  mixed   $default  The default value to return if the key is not found.
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Returns a boolean indicating whether or not the key exists in the repository.
     * 
     * @param  string  $key  The key to check.
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Sets the specified key to the specified value.
     * 
     * @param  string  $key  The key to be set.
     * @param  mixed   $value  The value to set the key to.
     * @return void
     */
    public function set(string $key, $value): void;
}
