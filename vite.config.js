import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const host = env.VITE_HOST || '0.0.0.0';
    const port = Number(env.VITE_PORT || 5173);
    const publicHost = env.VITE_PUBLIC_HOST || '127.0.0.1';
    const protocol = env.VITE_DEV_SERVER_PROTOCOL || 'http';

    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
            tailwindcss(),
        ],
        server: {
            host,
            port,
            strictPort: true,
            origin: `${protocol}://${publicHost}:${port}`,
            hmr: {
                host: publicHost,
            },
            watch: {
                ignored: ['**/storage/framework/views/**'],
            },
        },
    };
});
