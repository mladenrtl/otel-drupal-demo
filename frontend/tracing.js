'use strict';

const { diag, DiagConsoleLogger, DiagLogLevel } = require('@opentelemetry/api');

const opentelemetry= require('@opentelemetry/sdk-node');
const { Resource } = require('@opentelemetry/resources');
const {
  SEMRESATTRS_SERVICE_NAME,
} = require('@opentelemetry/semantic-conventions');
const { getNodeAutoInstrumentations } = require("@opentelemetry/auto-instrumentations-node");

diag.setLogger(new DiagConsoleLogger(), DiagLogLevel.DEBUG);

// enable all auto-instrumentations from the meta package
const sdk = new opentelemetry.NodeSDK({
  resource: new Resource({
    [SEMRESATTRS_SERVICE_NAME]: `ddev-rtl-web-frontend`,
  }),
  instrumentations: [getNodeAutoInstrumentations(
    {
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
