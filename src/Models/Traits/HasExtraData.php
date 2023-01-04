<?php

namespace Marshmallow\NovaFormbuilder\Models\Traits;

trait HasExtraData
{
    public function data(string $key)
    {
        $data = $this->getDataArray();
        if (!array_key_exists($key, $data)) {
            return null;
        }

        return $data[$key];
    }

    public function addExtraData(array $data, $column = 'extra_data'): void
    {
        $current_data = $this->getDataArray();
        $new_data = array_merge($current_data, $data);
        $this->update([
            $column => json_encode($new_data),
        ]);
    }

    protected function getDataArray($column = 'extra_data'): array
    {
        return ($this->$column) ? json_decode($this->$column, true) : [];
    }

    public function parseExtraData($column = 'extra_data'): object
    {
        return (object) json_decode($this->$column);
    }

    public function setExtraData($column, $value)
    {
        $this->extra_data[$column] = $value;
        return $this;
    }

    public function getExtraDataCast($column)
    {
        $column = 'mm_extra_' . $column;
        return $this->$column;
    }

    public function setExtraDataCast($column, $value)
    {
        $column = 'mm_extra_' . $column;
        $this->$column = $value;
        return;
    }

    public function storeExtraData($column, $value)
    {
        return $this->update(["extra_data->{$column}" => $value]);
    }

    public function getExtraData($column)
    {
        if ($this->extra_data) {
            return $this->extra_data[$column] ?? '';
        }
    }
}
