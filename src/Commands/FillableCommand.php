<?php

namespace Sheidin\Fillable\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FillableCommand extends Command
{
    public string $signature = 'model:fillable
    {model?* : The name of the model}
    {--no-override : Whether the fillable attribute should not be override}';

    public string $description = 'My command';
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
            $filePath = config('fillable.models_directory') . '/' . $file;
            $content = file_get_contents($filePath);
            $model = last(explode('/', Str::replaceLast('.php', '', ucfirst($file))));
            $this->comment("Adding fillable for model $model");
            /*Run pint to format the file*/
            $output = shell_exec(base_path('vendor/sheidin/fillable/pint') . ' ' . $filePath);
            /*Create instance of the model*/
            $class = app()->make(Str::betweenFirst($content, 'namespace ', ';') . '\\' . $model);
            /*Get list of columns from the DB*/
            $columns = array_diff(\Schema::getColumnListing($class->getTable()), config('fillable.ignore_columns'));
            if (count($columns)) {
                /*Replace or append fillable field to the model file*/
                $start = 'protected $fillable = [';
                $end = '];';
                $fillable = $start . "\n'" . implode("',\n'", $columns) . "'\n$end\n";
                if (count($class->getFillable()) && Str::contains($content, '$fillable')) {
                    if($this->override) {
                        file_put_contents($filePath, Str::replaceFirst($start . Str::betweenFirst($content, $start, $end) . $end, $fillable, $content));
                    }else{
                        $this->info("Model $model will not be overwritten");
                    }
                } elseif (Str::contains($content, 'public ')) {
                    file_put_contents($filePath, Str::replaceFirst("public ", "$fillable public ", $content));
                } else {
                    file_put_contents($filePath, Str::replaceFirst("}", "$fillable }", $content));
                }
                /*reformat the file*/
                $output = shell_exec(base_path('vendor/sheidin/fillable/pint') . ' ' . $filePath);
            }
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());
        }
    }
}
