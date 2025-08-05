<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Connection;

trait TenantSchema
{
    protected static ?string $currentTenantSchema = null;

    public static function currentSchema(): ?string
    {
        return static::$currentTenantSchema;
    }

    public static function setCurrentSchema(string $schema): void
    {
        static::$currentTenantSchema = $schema;
    }

    public static function forgetCurrentSchema(): void
    {
        DB::statement("SET search_path TO public");
        static::$currentTenantSchema = null;
    }

    public static function setSearchPath(): void
    {
        $schema = static::currentSchema();
        if (empty($schema)) {
            throw new \Exception('No tenant schema set.');
        }
        static::currentConnection()->statement("SET search_path TO \"{$schema}\", public");
    }

    public static function forceReconnect(): void
    {
        $connection = static::getTenantConnectionName();
        config([
            "database.connections.{$connection}.search_path" => static::currentSchema() . ', public',
        ]);
    }

    public static function currentConnection(): Connection
    {
        return DB::connection(static::getTenantConnectionName());
    }

    public static function getTenantConnectionName()
    {
        return config('multitenancy.tenant_database_connection_name') ?? 'tenant';
    }

    public function makeCurrent(bool $forceReconnect = false): static
    {
        if ($this->isCurrent()) {
            return $this;
        }

        static::forgetCurrentSchema();

        $schema = $this->getAttribute('schema');
        if (empty($schema)) {
            throw new \Exception('Tenant schema is not defined.');
        }

        static::setCurrentSchema($schema);

        if ($forceReconnect) {
            static::forceReconnect();
        }

        static::setSearchPath();

        return $this;
    }

    public function forget(): static
    {
        static::forgetCurrentSchema();
        return $this;
    }

    public function isCurrent(): bool
    {
        return static::currentSchema() === $this->getAttribute('schema');
    }

    public function getDatabaseName(): string
    {
        return $this->getAttribute('schema');
    }
}
