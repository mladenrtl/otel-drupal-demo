// server.js
const { createServer } = require('http');
const { parse } = require('url');
const next = require('next');
const sdk = require('./tracing');

const dev = process.env.NODE_ENV !== 'production';
const hostname = 'localhost';
const port = 3003;
// when using middleware `hostname` and `port` must be provided below
const app = next({ dev, hostname, port });
const handle = app.getRequestHandler();

// initialize the SDK and register with the OpenTelemetry API
// this enables the API to record telemetry
sdk.start();
console.log('Tracing initialized');
startServer();

function startServer() {
  app.prepare().then(() => {
    createServer(async (req, res) => {
      try {
        // Be sure to pass `true` as the second argument to `url.parse`.
        // This tells it to parse the query portion of the URL.
        const parsedUrl = parse(req.url, true);

        await handle(req, res, parsedUrl);
      } catch (err) {
        console.error('Error occurred handling', req.url, err);
        res.statusCode = 500;
        res.end('internal server error');
      }
    }).listen(port, (err) => {
      if (err) throw err;
      console.log(`> Ready on http://${hostname}:${port}`);
    });
  });
}
