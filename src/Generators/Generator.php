<?php

namespace Owlcoder\Generators;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\MySqlSchemaManager;

use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\BinaryType;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Psy\Util\Str;

class Generator
{
    /** @var string */
    public $baseModelClass = '\\App\\BaseModel';

    /** @var string */
    public $namespace = 'App\Models\Base';

    public $className = null;

    /** @var Table */
    public $table;

    public $template = __DIR__ . '/../../resources/templates/base-model.php';

    public function __construct($options = [])
    {
        foreach ($options as $key => $value) {
            $this->$key = $value;
        }
    }

    public function generate()
    {
        /** @var MySqlSchemaManager $schemaManager */
        $table = $this->table;

        $extends = $this->baseModelClass;
        $shortClassName = substr($extends, strrpos($extends, '\\') + 1);

        $result = $this->renderBaseModel([
            'fields' => $this->prepareFields($table->getColumns()),
            'namespace' => $this->namespace,
            'extendsShortClassName' => $shortClassName,
            'extendsFullClassName' => $this->baseModelClass,
            'className' => $this->className ?? ucfirst(\Illuminate\Support\Str::camel($table->getName())),
            'table' => $table->getName(),
            'fieldsArr' => "[" . join(', ', array_map(function (Column $column) {
                    return "'" . $column->getName() . "'";
                }, $this->filterFillableColumns($table->getColumns()))) . "]",
        ]);

        return $result;
    }

    public function filterFillableColumns($columns)
    {
        return array_values(array_filter($columns, function ($col) {
            return $col->getName() != 'id';
        }));
    }

    /**
     * @param Column[] $columns
     */
    public function prepareFields($columns)
    {
        $out = [];

        foreach ($columns as $one) {
            $out[] = [
                'name' => $one->getName(),
                'type' => $this->mapDbTypeToPhp($one->getType()->getName()),
            ];
        }

        return $out;
    }

    public function mapDbTypeToPhp($type)
    {
        return [
            Types::ARRAY => '?',
            Types::BIGINT => 'integer',
            Types::BINARY => 'boolean',
            Types::BLOB => 'string',
            Types::BOOLEAN => 'boolean',
            Types::DATE_MUTABLE => 'string',
            Types::DATE_IMMUTABLE => 'string',
            Types::DATEINTERVAL => 'string',
            Types::DATETIME_MUTABLE => 'string',
            Types::DATETIME_IMMUTABLE => 'string',
            Types::DATETIMETZ_MUTABLE => 'string',
            Types::DATETIMETZ_IMMUTABLE => 'string',
            Types::DECIMAL => 'double',
            Types::FLOAT => 'float',
            Types::GUID => 'string',
            Types::INTEGER => 'integer',
            Types::JSON => 'string',
            Types::JSON_ARRAY => 'string',
            Types::OBJECT => 'string',
            Types::SIMPLE_ARRAY => '?',
            Types::SMALLINT => 'integer',
            Types::STRING => 'string',
            Types::TEXT => 'string',
            Types::TIME_MUTABLE => 'string',
            Types::TIME_IMMUTABLE => 'string',
        ][$type];
    }

    public function renderBaseModel($variables)
    {
        extract($variables);
        ob_start();

        require $this->template;

        return ob_get_clean();
    }
}
