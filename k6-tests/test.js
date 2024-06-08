import http from 'k6/http';
import { check, sleep } from 'k6';
import { urls }  from "./urls.js";

export const options = {
  vus: `${__ENV.NUMBER_OF_VUS}`, // Key for Smoke test. Keep it at 2, 3, max 5 VUs
  duration: `${__ENV.DURATION}`, // This can be shorter or just a few iterations
};

export default () => {
  urls().forEach((url) => {
    const urlRes = http.get(url);
    check(urlRes, {
      'status is 200': (r) => r.status === 200,
    });
    sleep(2);
  });
};
