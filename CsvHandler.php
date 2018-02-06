<?php
/**
 * Created by PhpStorm.
 * User: michele.papagni
 * Date: 06/02/18
 * Time: 15:00
 */

namespace MYNamespace;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection as DBCollection;

/**
 * Class CsvLibrary
 * @package Mike\Libraries
 */
class CsvHandler
{
    protected $data;
    protected $fileName;
    protected $columns;
    protected $path;

    /**
     * @param $data
     * @return CsvLibrary
     * @throws \Exception
     */
    public function data($data): self
    {
        if ($data instanceof EloquentCollection) {
            $data = $data->toArray();
        } else if ($data instanceof DBCollection) {
            $data = json_decode(json_encode($data->toArray()), TRUE);
        }

        if (!is_array($data)) throw new \Exception('An error occurred');

        $this->data = $data;

        return $this;
    }

    /**
     * @param $fileName
     * @return $this
     * @throws \Exception
     */
    public function fileName($fileName): self
    {
        if (strpos($fileName, '.csv') === false || strlen($fileName) < 5) throw new \Exception('An error occurred');

        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @param $columns
     * @return $this
     * @throws \Exception
     */
    public function columns($columns)
    {
        if (!is_array($columns)) throw new \Exception('An error occurred');

        $this->columns = $columns;

        return $this;
    }

    /**
     * @param $path
     * @return $this
     * @throws \Exception
     */
    public function path($path)
    {
        if (!is_string($path) || strlen($path) < 5) throw new \Exception('An error occurred');

        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function download()
    {
        $this->processPath();
        $this->processColumns();
        $this->processData();

        if (!$this->outputIsValid()) throw new \Exception('An error occurred');

        $csv = $this->openCsv();
        $this->addHeader($csv);
        $this->addData($csv);
        $this->closeCsv($csv);

        return $this->path.$this->fileName;
    }

    /**
     *
     */
    private function processPath()
    {
        if (empty($this->path)) {
            $this->path  = storage_path('app/tmp/');
        }
    }

    /**
     *
     */
    private function processColumns(): void
    {
        if (empty($this->columns)) $this->columns = collect($this->data[0])->keys()->all();
    }

    /**
     *
     */
    private function processData()
    {
        foreach ($this->data as $k => $datum) {
            $this->data[$k] = collect($datum)->only($this->columns)->all();
        }
    }

    /**
     * @return bool
     */
    private function outputIsValid()
    {
        return !empty($this->data) && !empty($this->fileName) && !empty($this->columns);
    }

    /**
     * @return bool|resource
     */
    private function openCsv()
    {
        $csv = fopen($this->path.$this->fileName, 'w');

        return $csv;
    }

    /**
     * @param $csv
     */
    private function addHeader($csv)
    {
        fputcsv($csv, $this->columns);
    }

    /**
     * @param $csv
     */
    private function addData($csv)
    {
        foreach ($this->data as $datum) {
            fputcsv($csv, $datum);
        }
    }

    /**
     * @param $csv
     */
    private function closeCsv($csv)
    {
        fclose($csv);
    }

}