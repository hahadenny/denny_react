import axios from 'axios';

const instance = axios.create({
  baseURL: 'http://localhost/denny_react/public',
  timeout: 1000,
  headers: {'X-Custom-Header': 'foobar'}
});
export default instance;
