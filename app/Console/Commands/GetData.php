<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GetData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:data';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $client = new \GuzzleHttp\Client();
        $domains = ['https://id.123dok.com', 'https://1library.net', 'https://1library.co'];
        foreach ($domains as $domain) {
            foreach (config('report') as $table_name => $report) {
                try {
                    $response = $client->get($domain . $report['api']);
                } catch (\Exception $exception){
                    $this->error($exception->getMessage());
                }
                $response_data = json_decode($response->getBody()->getContents(), true);
                $data_field = isset($report['custom_data_fields']) ? $report['custom_data_fields'] : 'items';
                if (!Schema::hasTable($table_name) && isset($response_data[$data_field][0])) {
                    $field_list = array_merge($report['fields'], array_keys($report['attribute_mapping']));
                    $field_list[] = 'domain';
                    $field_list[] = 'addition';
                    $this->createSchema($field_list, $table_name, $report['attribute_mapping']);
                }
                
                foreach ($response_data[$data_field] as $key => $data) {
                    $attributes = [];
                    foreach ($report['unique_fields'] as $unique_field) {
                        $attributes[$unique_field] = $data[$unique_field];
                    }
                    $values = [];
                    $values['domain'] = $attributes['domain'] = str_replace("https://", "", $domain);
                    foreach ($data as $field => $datum) {
                        if ($field == 'id') {
                            continue;
                        }
                        if (in_array($field, array_merge($report['fields'], array_keys($report['attribute_mapping'])))) {
                            $values[$field] = $datum;
                        } else {
                            $addition_value[$field] = $datum;
                        }
                    }
                    if (!empty($addition_value)) {
                        $values['addition'] = json_encode($addition_value);
                    }
                    $values['created_at'] = Carbon::now();
                    $values['updated_at'] = Carbon::now();
                    DB::table($table_name)->updateOrInsert($attributes, $values);
                }
            }
        }
        return 0;
        
    }
    
    protected function getAttributeMapping($attributes = [])
    {
        return array_merge([
            'date' => 'date',
            'count' => 'unsignedInteger',
            'domain' => 'string',
            'addition' => 'json',
            'report_date' => 'date',
            'bot' => 'string',
            'min_execution_time' => 'unsignedInteger',
            'max_execution_time' => 'unsignedInteger',
            'average_execution_time' => 'unsignedInteger',
            'total' => 'unsignedInteger',
            'public_count' => 'unsignedInteger',
        
        ], $attributes);
    }
    
    public function createSchema($fields, $table, $attributes = [])
    {
        Schema::create($table, function (Blueprint $table) use ($attributes, $fields) {
            $table->increments('id');
            foreach ($fields as $field) {
                if ($field == 'id')
                    continue;
                switch ($this->getAttributeMapping($attributes)[$field]) {
                    case 'date':
                        $table->date($field)->index();
                        break;
                    case 'unsignedInteger':
                        $table->unsignedInteger($field)->default(0);
                        break;
                    case 'integer':
                        $table->integer($field)->default(0);
                        break;
                    case 'string':
                        $table->string($field);
                        break;
                    case 'json':
                        $table->json($field)->nullable();
                        break;
                    case 'datetime':
                        $table->dateTime($field);
                }
            }
            $table->timestamps();
        });
    }
    
}
