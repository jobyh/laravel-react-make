<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase;
use Illuminate\Filesystem\Filesystem;
use Mockery\MockInterface;
use Jobyh\ReactMake\Support\CommandsProvider;

class ReactComponentCommandTest extends TestCase {

    protected function getPackageProviders($app)
    {
        return [CommandsProvider::class];
    }

    public function test_it_requires_name_parameter()
    {
        $this->expectErrorMessage('missing: "name"');

        Artisan::call('make:react');
    }

    public function test_it_checks_if_component_already_exists()
    {
        $this->mock(Filesystem::class, function(MockInterface $mock) {
            $filepath = resource_path('js/components/TestComponent.js');

            // Stubs.
            $mock->allows([
                'isDirectory' => true,
                'get' => 'template content',
                'put' => 23,
            ]);

            // Behaviour under test.
            $mock->shouldReceive('exists')
                ->with($filepath)
                ->once()
                ->andReturn(false);

            // The method is subsequently called
            // but this is not the call under test.
            $mock->shouldReceive('exists')->once();
        });

        $result = Artisan::call('make:react', ['name' => 'TestComponent']);
        $this->assertSame(0, $result);
    }

    public function test_it_creates_directories()
    {
        $this->mock(Filesystem::class, function(MockInterface $mock) {

            // Stubs.
            $mock->allows([
                'exists' => false,
                'isDirectory' => false,
                'get' => 'template content',
                'put' => 23,
            ]);

            $dirpath = resource_path('js/components/foo/bar');

            $mock->shouldReceive('makeDirectory')
                ->withArgs([$dirpath, 0777, true, true])
                ->once()
                ->andReturn(true);
        });

        $result = Artisan::call('make:react', ['name' => 'TestComponent', '--dir' => 'foo/bar']);
        $this->assertSame(0, $result);
    }

    public function test_it_retrieves_stub()
    {
        $this->mock(Filesystem::class, function(MockInterface $mock) {
            // Stubs.
            $mock->allows([
                'exists' => false,
                'isDirectory' => true,
                'put' => 890,
            ]);

            $mock->shouldReceive('get')
                ->with(realpath(__DIR__ . '/../stubs/react.stub'))
                ->once();
        });

        $result = Artisan::call('make:react', ['name' => 'TestComponent']);
        $this->assertSame(0, $result);
    }

    public function test_it_writes_component()
    {
        $this->mock(Filesystem::class, function(MockInterface $mock) {
            // Stubs.
            $mock->allows([
                'exists' => false,
                'isDirectory' => true,
                'get' => 'template content',
            ]);

            $mock->shouldReceive('put')
                ->withArgs([resource_path('js/components/TestComponent.js'), 'template content'])
                ->once();
        });

        $result = Artisan::call('make:react', ['name' => 'TestComponent']);
        $this->assertSame(0, $result);
    }

    public function test_it_uses_jsx_extension()
    {
        $this->mock(Filesystem::class, function(MockInterface $mock) {
            // Stubs.
            $mock->allows([
                'exists' => false,
                'isDirectory' => true,
                'get' => 'template content',
            ]);

            $mock->shouldReceive('put')
                ->withArgs([resource_path('js/components/TestComponent.jsx'), 'template content'])
                ->once();
        });

        $result = Artisan::call('make:react', ['name' => 'TestComponent', '--jsx' => true]);
        $this->assertSame(0, $result);
    }

    public function test_it_uses_overridden_stubs()
    {
        File::deleteDirectory(base_path('stubs'));
        File::deleteDirectory(resource_path('js/components'));

        $stubPath = base_path('stubs/react.stub');
        File::makeDirectory(dirname($stubPath));
        File::put($stubPath, 'Overridden stub');

        $result = Artisan::call('make:react', ['name' => 'TestComponent']);

        $this->assertSame(0, $result);
        $this->assertSame('Overridden stub', File::get(resource_path('js/components/TestComponent.js')));
    }

    public function test_it_publishes_stubs()
    {
        File::deleteDirectory(base_path('stubs'));
        File::deleteDirectory(resource_path('js/components'));

        $result = Artisan::call('vendor:publish', ['--tag' => 'react-stub']);

        $this->assertSame(0, $result);

        foreach (CommandsProvider::stubs() as $stub) {
            $this->assertTrue(File::exists(base_path("stubs/{$stub}")), "Stub {$stub} was not published.");
        }
    }

    public function test_it_uses_class_component_stub()
    {
        File::deleteDirectory(base_path('stubs'));
        File::deleteDirectory(resource_path('js/components'));

        $this->mock(Filesystem::class, function(MockInterface $mock) {
            // Stubs.
            $mock->allows([
                'exists' => false,
                'isDirectory' => true,
                'put' => 23,
            ]);

            $mock->shouldReceive('get')
                ->with(realpath(__DIR__ . '/../stubs/react-class.stub'))
                ->once();
        });

        $result = Artisan::call('make:react', ['name' => 'TestComponent', '--class' => true]);

        $this->assertSame(0, $result);
    }
}