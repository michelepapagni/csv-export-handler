# Csv Export Handler
This is a simple csv export handler

## Getting Started
Import the library as library into your laravel project

### Instantiate in your controller
```
  $csvLibrary = new CsvHandler;
```
### Use the method to set the csv 
e.g.
```
$csv = $csvLibrary
    ->data(Model::all())
    ->columns(['id', 'name'])
    ->fileName('test.csv')
    ->path(storage_path('app/csv/'))
    ->download();

    return response()->download($csv);
```

### Method:
->data: You can use Eloquent query, Query builder query (DB) or a simple array (required)
->columns: use it if you want to export only some columns (optional)
->fileName: this is the name of the exported file (required)
->path: you can specify the path of the saved file (default: storage/app/tmp/) (optional)
->download(): use to create the download file (required)
