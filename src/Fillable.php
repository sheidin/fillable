<?php

namespace Sheidin\Fillable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Fillable
{
    public string $content;
    public string $file;
    private string $filePath;
    public Model $model;
    public array $columns;


    /**
     * @param string $file
     * @return static
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function load(string $file): self
    {
        $instance = new static();
        $instance->file = $file;
        $instance->filePath = config('fillable.models_directory') . '/' . $instance->file;
        $instance->content = file_get_contents($instance->filePath);
        $model = last(explode('/', Str::replaceLast('.php', '', ucfirst($instance->file))));
        $instance->model = $class = app()->make(Str::betweenFirst($instance->content, 'namespace ', ';') . '\\' . $model);
        $instance->columns = $instance->getColumns();
        return $instance;
    }

    /**
     * @param bool $override
     * @return bool|int
     */
    public function addFillableToFile(bool $override = true): bool|int
    {
        $start = 'protected $fillable = [';
        $end = '];';
        if (count($this->model->getFillable()) && Str::contains($this->content, '$fillable')) {
            if ($override) {
                return file_put_contents($this->filePath, Str::replaceFirst($start . Str::betweenFirst($this->content, $start, $end) . $end, $this->fillableString(), $this->content));
            }
        }/* elseif (Str::contains($this->content, 'public ')) {
            return file_put_contents($this->filePath, Str::replaceFirst("public ", "{$this->fillableString()} public ", $this->content));
        }*/ else {
            return file_put_contents($this->filePath, Str::replaceFirst("}", "{$this->fillableString()} }", $this->content));
        }
        return false;
    }

    /**
     * @return bool
     */
    public function hasColumns(): bool
    {
        return count($this->columns);
    }

    /**
     * @return string
     */
    private function fillableString(): string
    {
        return $this->start . "\n'" . implode("',\n'", $this->columns) . "'\n$this->end\n";
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return array_diff(\Schema::getColumnListing($this->model->getTable()), config('fillable.ignore_columns'));
    }

    /**
     * @return bool|string|null
     */
    public function format(): bool|string|null
    {
        return shell_exec(base_path('vendor/sheidin/fillable/pint') . ' ' . $this->file);
    }
}
