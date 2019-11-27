<?php

class GeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Illuminate\Console\Application
     */
    public $app;

    public function setUp(): void
    {
        /**
         * @var \Illuminate\Foundation\Application
         */
        $this->app = require __DIR__ . '/../../../../bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
    }

    public function testGenerator()
    {
        $gen = new \Owlcoder\Generators\Generator([
            'extends' => '\App\BaseModel',
            'db' => \Illuminate\Support\Facades\DB::connection(),
        ]);

        $gen->inspectModels();
    }
}
