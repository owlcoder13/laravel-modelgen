<?php

namespace Owlcoder\Console\Commands;

use Doctrine\DBAL\Schema\MySqlSchemaManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Owlcoder\Generators\Generator;

class GenerateModels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:models';

    public function handle()
    {
        /** @var MySqlSchemaManager $schemaManager */
        $schemaManager = \DB::connection()->getDoctrineSchemaManager();
        foreach ($schemaManager->listTables() as $table) {
            $modelClassName = ucfirst(Str::camel($table->getName()));

            /**
             * Generate base model
             */
            $baseClassName = 'Base' . $modelClassName;

            $baseModelGenerator = new Generator([
                'table' => $table,
                'baseModelClass' => '\\App\\BaseModel',
                'namespace' => 'App\\Models\\Base',
                'className' => $baseClassName,
            ]);

            $path = base_path('app/Models/Base/' . $baseClassName . '.php');
            $this->output->writeln("generate $path");

            $directory = dirname($path);
            if(!file_exists($directory)){
                mkdir($directory, 0777, true);
            }

            file_put_contents($path, $baseModelGenerator->generate());

            /**
             * Generate model
             */
            $path = base_path('app/Models/' . $modelClassName . '.php');

            if ( ! file_exists($path)) {
                $modelClassName = ucfirst(Str::camel($table->getName()));

                $baseModelGenerator = new Generator([
                    'table' => $table,
                    'baseModelClass' => '\\App\\Models\\Base\\' . $baseClassName,
                    'namespace' => 'App\\Models',
                    'className' => $modelClassName,
                    'template' => __DIR__ . '/../../../resources/templates/model.php'
                ]);

                $this->output->writeln("generate $path");
                file_put_contents($path, $baseModelGenerator->generate());
            } else {
                $this->output->writeln("exists... skip $path");
            }
        }
    }
}
