
## List of placeholders

### {{ ModelClassName }}
```php
str($this->table_name)->studly()->singular()->toString();
```
**Updater:** `PulsarLabs\Generators\Support\Updaters\ModelClassNameUpdater`

**Usage:**
```php
$stub = (new ModelClassNameUpdater($stub, $table_name))->handle();
```

### {{ ModelVariable }}
```php
str($table_name)->singular()->slug('_')->toString();
```
**Updater:** `PulsarLabs\Generators\Support\Updaters\ModelVariableUpdater`

**Usage:**
```php
$stub = (new ModelVariableUpdater($stub, $table_name))->handle();
```
