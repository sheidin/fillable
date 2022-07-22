<?php

namespace Sheidin\Fillable\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Sheidin\Fillable\Fillable;


class FillableCommand extends Command
{
    public $signature = 'model:fillable
    {model?* : The name of the model}
    {--no-override : Whether the fillable attribute should not be override}';

    public $description = 'My command';
    private bool $override = true;

    /**
     * @return void
     */
    public function handle(): void
    {
        $models = $this->argument('model');
        $this->override = !(($this->option('no-override') == "true"));
        /*Filter by specific models*/
        array_walk($models, function (&$model) {
            $model = Str::studly($model) . ".php";
        });
        /*Get list of models*/
        $files = array_filter(scandir(config('fillable.models_directory')), function ($file) use ($models) {
            return (count($models)) ? (Str::endsWith($file, ".php") && Str::contains($file, $models)) : Str::endsWith($file, ".php");
        });
        if (count($files)) {
            foreach ($files as $file) {
                $this->addFillableToFile($file);
            }
        } else {
            $this->info("No models found");
        }
    }

    /**
     * @param string $file
     * @return void
     */
    private function addFillableToFile(string $file): void
    {
        try {
            $fillable = Fillable::load($file);
            $this->comment("Adding fillable to model {$fillable->modelName()}");
            /*Run pint to format the file*/
            $fillable->format();
            /*Get list of columns from the DB*/
            if ($fillable->hasColumns()) {
                /*Write changes to the original model*/
                ($fillable->addFillableToFile($this->override)) ? $fillable->format() : $this->warn("Failed to write to file");
            }else{
                $this->warn("Failed to find columns in DB");
            }
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());
        }
    }
}
