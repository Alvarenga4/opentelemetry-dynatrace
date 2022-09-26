<?php

declare(strict_types=1);
require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use OpenTelemetry\SDK\Trace\SpanExporter\ConsoleSpanExporter;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\Contrib\OtlpHttp\Exporter as OtlpHttpExporter;
use OpenTelemetry\SDK\Trace\TracerProvider;

echo 'Starting OtlpHttpExporter' . PHP_EOL;

putenv('OTEL_EXPORTER_OTLP_ENDPOINT=https://mmb00002.live.dynatrace.com/api/v2/otlp/v1/traces'); //Dynatrace OTLP ENDPOINT
putenv('OTEL_SERVICE_NAME=dynatrace_plataforma_test');

$exporter = new OtlpHttpExporter(
  new Client(),
  new HttpFactory(),
  new HttpFactory()
);

$tracerProvider =  new TracerProvider(
  new SimpleSpanProcessor(
    $exporter
  )
);

$tracer = $tracerProvider->getTracer('io.opentelemetry.contrib.php',);

$root = $span = $tracer->spanBuilder('root')->startSpan();
$span->activate();

for ($i = 0; $i < 3; $i++) {
  // start a span, register some events
  $span = $tracer->spanBuilder('loop-' . $i)->startSpan();

  $span->setAttribute('remote_ip', '1.2.3.4')
    ->setAttribute('country', 'USA');

  $span->end();
}

print_r($tracerProvider);

$root->end();
