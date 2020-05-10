<?php
namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use OpenTracing\GlobalTracer;
use Zipkin\Endpoint;
use Zipkin\Reporters\Http;
use Zipkin\Reporters\Http\CurlFactory;
use Zipkin\Samplers\BinarySampler;
use Zipkin\TracingBuilder;
use ZipkinOpenTracing\Tracer;
use const OpenTracing\Formats\TEXT_MAP;

class TracingServiceProvider extends ServiceProvider
{
    public static $frameworkSpan;

    public function register()
    {
        // 单例模式绑定追踪器
        $this->app->singleton('Tracing', function () {
            $endpoint = Endpoint::create('Manage-Api', '127.0.0.1', null, 8000);
            $reporter = new Http(
                CurlFactory::create(),
                ['endpoint_url' => 'http://192.168.2.8:9411/api/v2/spans']
            );
            $sampler = BinarySampler::createAsAlwaysSample();
            $tracing = TracingBuilder::create()
                ->havingLocalEndpoint($endpoint)
                ->havingSampler($sampler)
                ->havingReporter($reporter)
                ->build();


            GlobalTracer::set(new Tracer($tracing));

            return GlobalTracer::get();
        });


        $this->app["db"]->connection()->setEventDispatcher($this->app["events"]);
        DB::listen(function ($query){
            app('Tracing')->startActiveSpan('DB Query',  [
                'tags' => [
                    'SQL' => $query->sql,
                    'Bindings' => json_encode($query->bindings),
                    'Cost(/ms)' => $query->time
                ],
                'start_time' => intval(\Zipkin\Timestamp\now() - intval($query->time * 1000)),
            ])->close();
        });

        register_shutdown_function(function() {
            TracingServiceProvider::$frameworkSpan->close();
            GlobalTracer::get()->flush();
        });
    }

    public function boot()
    {
        $carrier = array_map(function ($header) {
            return $header[0];
        }, app('request')->headers->all());

        // 开始追踪
        $parent_span = app('Tracing')->extract(TEXT_MAP, $carrier);


        TracingServiceProvider::$frameworkSpan = app('Tracing')->startActiveSpan('Bootstrap', ['child_of' => $parent_span]);
    }
}
