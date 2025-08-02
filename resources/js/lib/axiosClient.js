// resources/js/lib/axios.js
import axios from 'axios';
import { router } from '@inertiajs/vue3'; // For redirects if needed

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
  axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// ✅ Request interceptor
axios.interceptors.request.use(
  config => {
    // e.g., attach bearer token if needed
    // config.headers.Authorization = `Bearer ${yourToken}`;
    return config;
  },
  error => {
    return Promise.reject(error);
  }
);

// ✅ Response interceptor
axios.interceptors.response.use(
  response => {
    return response;
  },
  error => {
    // Automatically handle common Laravel responses
    if (error.response) {
      const status = error.response.status;

      if (status === 401) {
        console.error('Unauthorized - redirecting to login');
        router.visit('/login');
      }

      if (status === 403) {
        console.warn('Forbidden - access denied');
      }

      if (status === 422) {
        console.warn('Validation failed:', error.response.data.errors);
      }
      // Optionally handle other errors
    }

    return Promise.reject(error);
  }
);

export default axios;
