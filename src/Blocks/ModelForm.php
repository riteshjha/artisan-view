<?php

namespace Sven\ArtisanView\Blocks;

use Illuminate\Support\Facades\DB;
use Str;

class ModelForm extends Block
{
    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return "@section('content')" . PHP_EOL . PHP_EOL . $this->getContents() . PHP_EOL . PHP_EOL . '@endsection' . PHP_EOL . PHP_EOL;
    }

    /**
     * Override parent contents and return form for table fields
     *
     * @return void
     */
    public function getContents()
    {
        $modelName = parent::getContents();

        $model = new $modelName();

        return $this->generateForm($this->columns($model));
    }

    /**
     * return table columns
     * 
     * @return array
     */
    protected function columns($model)
    {
        return DB::select('show columns from ' . $model->getTable());
    }

    /**
     * Generate form 
     *
     * @param array $fields
     * @return void
     */
    protected function generateForm($columns)
    {
        $content = <<<EOF
        <div class="container-fluid">
            <form role="form" method="post">
                @csrf
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Title</h3>
                    </div>
                    <div class="card-body">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">

        EOF;


        collect($columns)->map(function ($value, $key) use (&$content) {

            $field = $value->Field;
            $label = Str::studly($field);

            if (in_array($field, ['id', 'created_by', 'updated_by', 'deleted_by', 'deleted_at', 'created_at', 'updated_at'])) return;

            $content .= <<<EOF
                                        <div class="form-group">
                                            <label for="$field">$label</label>
                                            <div class="input-group">
                                                <input type="text" name="$field" class="form-control @error('$field') is-invalid @enderror" 
                                                        value="{{ old('$field') }}" placeholder="$label">
                                                @if(\$errors->has('$field'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ \$errors->first('$field') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

            EOF;
        });

        $content .= <<<EOF
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-default bg-gradient-primary float-right btn-sm">Create</button>
                    </div>
                </div>
            </form>
        </div>
        EOF;

        return $content;
    }
}
