<?php


namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait DatabaseSetup
{
    protected static $migrated = false;

    public function setupDatabase()
    {
        if ($this->isInMemory()) {
            $this->setupInMemoryDatabase();
        } else {
            $this->setupTestDatabase();
        }
    }

    protected function isInMemory()
    {
        return config('database.connections')[config('database.default')]['database'] == ':memory:';
    }

    protected function setupInMemoryDatabase()
    {
        $this->artisan('migrate:fresh --seed');
        $this->app[Kernel::class]->setArtisan(null);
    }

    protected function setupTestDatabase()
    {
        if (!static::$migrated) {
            $this->whenMigrationsChange(function() {
                $this->artisan('migrate:fresh --seed');
                $this->app[Kernel::class]->setArtisan(null);
            });
            static::$migrated = true;
        }
        $this->beginDatabaseTransaction();
    }

    public function beginDatabaseTransaction()
    {
        $database = $this->app->make('db');
        foreach ($this->connectionsToTransact() as $name) {
            $database->connection($name)->beginTransaction();
        }
        $this->beforeApplicationDestroyed(function () use ($database) {
            foreach ($this->connectionsToTransact() as $name) {
                $database->connection($name)->rollBack();
            }
        });
    }

    protected function connectionsToTransact()
    {
        return property_exists($this, 'connectionsToTransact')
            ? $this->connectionsToTransact : [null];
    }

    protected function getMigrationsMd5()
    {
        return md5(collect(glob(base_path('database/migrations/*')))
            ->map(function ($f) {
                return file_get_contents($f);
            })->implode(''));
    }

    protected function whenMigrationsChange($callback)
    {
        $md5 = $this->getMigrationsMd5();
        $path = storage_path('app/migrations_md5.txt');
        if(!file_exists($path) || ($md5 !== file_get_contents($path))) {
            $callback();
            file_put_contents($path, $md5);
        }
    }
}
