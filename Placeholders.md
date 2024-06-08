
## List of placeholders

### {{ ModelClassName }}
```php
str($this->table_name)->studly()->singular()->toString();
```
**Processor:** `PulsarLabs\Generators\Support\Processors\ModelClassNameProcessor`

**Example:**
Table name: `collection_points`
Result: `CollectionPoint`

### {{ ModelVariable }}
```php
str($table_name)->singular()->slug('_')->toString();
```
**Processor:** `PulsarLabs\Generators\Support\Updaters\ModelVariableProcessor`

**Example:**
Table name: `collection_points`
Result: `collection_point`

### {{ ModelPluralLowercaseSpaces }}
```php
str($command_data->table_name)->lower()->replace('_', ' ')->plural()->toString();
```
**Processor:** `PulsarLabs\Generators\Support\Updaters\ModelPluralLowercaseSpacesProcessor`

**Example:**
Table name: `collection_points`
Result: `collection points`
