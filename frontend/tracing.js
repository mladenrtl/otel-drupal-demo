'use strict';

const opentelemetry= require('@opentelemetry/sdk-node');
const { SimpleSpanProcessor } = require('@opentelemetry/sdk-trace-node');
const { Resource } = require('@opentelemetry/resources');
const {
  SemanticResourceAttributes,
} = require('@opentelemetry/semantic-conventions');
const {
  OTLPTraceExporter,
} = require('@opentelemetry/exporter-trace-otlp-http');
const { getNodeAutoInstrumentations } = require("@opentelemetry/auto-instrumentations-node");

const traceExporter = new OTLPTraceExporter({
  url: process.env.OTEL_EXPORTER_OTLP_ENDPOINT,
});

// enable all auto-instrumentations from the meta package
const sdk = new opentelemetry.NodeSDK({
  traceExporter,
  resource: new Resource({
    [SemanticResourceAttributes.SERVICE_NAME]: `ddev-rtl-web-frontend`,
  }),
  instrumentations: [getNodeAutoInstrumentations(
    {
      // eslint-disable-next-line @typescript-eslint/naming-convention
      "@opentelemetry/instrumentation-fs": {
        enabled: false, // very noisy
      },
    }
  )],
});

// gracefully shut down the SDK on process exit
process.on('SIGTERM', () => {
  sdk.shutdown()
    .then(() => console.log('Tracing terminated'))
    .catch((error) => console.log('Error terminating tracing', error))
    .finally(() => process.exit(0));
});

module.exports = sdk;
