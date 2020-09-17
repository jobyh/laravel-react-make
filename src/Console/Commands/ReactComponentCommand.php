<?php

namespace Jobyh\ReactMake\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\App;

class ReactComponentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = <<<TEXT
make:react
    {name : The name of the React component}
    {--x|jsx : Use .jsx file extension}
    {--c|class : Create a class-based React component}
TEXT;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new React component';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    protected function getPath(string $name) : string
    {
        return App::resourcePath("js/components/{$name}.{$this->getExtension()}");
    }

    protected function getExtension() : string {
        return $this->option('jsx')
            ? 'jsx'
            : 'js';
    }

    protected function getStub() : string
    {
        $stub = $this->option('class')
            ? 'react-class.stub'
            : 'react.stub';
        $override = base_path("stubs/{$stub}");

        if ($this->files->exists($override)) {
            return $override;
        }

        return realpath(__DIR__ . "/../../../stubs/{$stub}");
    }

    protected function makeDirectory(string $path) : string
    {
        $dirpath = dirname($path);

        if (! $this->files->isDirectory($dirpath)) {
            return $this->files->makeDirectory($dirpath, 0777, true, true);
        }

        return $path;
    }

    protected function buildComponent(string $name) : string
    {
        $stub = $this->files->get($this->getStub()) ;

        return str_replace('DummyComponent', $name, $stub);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        $path = $this->getPath($name);

        // TODO if not interactive then throw.
        if ($this->files->exists($path) && ! $this->confirm("Overwrite existing component {$name}?")) {
            return;
        }

        $this->makeDirectory($path);
        $this->files->put($this->getPath($name), $this->buildComponent($name));
        $this->info($name . ' created successfully');
    }
}
