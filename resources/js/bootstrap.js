import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';

const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;
const pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER;

if (pusherKey && pusherCluster) {
    import('pusher-js').then((Pusher) => {
        window.Pusher = Pusher.default ?? Pusher;

        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: pusherKey,
            cluster: pusherCluster,
            forceTLS: true,
        });
    });
} else {
    window.Echo = { channel: () => ({ listen: () => {}, unsubscribe: () => {} }), private: () => ({ listen: () => {}, unsubscribe: () => {} }) };
}
