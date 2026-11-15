// Starts `php artisan serve`, finding PHP even when it is not on PATH.
// Override with the PHP_BINARY environment variable if needed.
import { spawn, spawnSync } from 'node:child_process';

const candidates = [process.env.PHP_BINARY, 'php', 'C:\\tools\\php84\\php.exe'].filter(Boolean);
const php = candidates.find((bin) => spawnSync(bin, ['-v']).status === 0);

if (!php) {
    console.error('PHP 8.3+ not found. Add it to PATH or set PHP_BINARY to the php executable.');
    process.exit(1);
}

const server = spawn(php, ['artisan', 'serve', ...process.argv.slice(2)], { stdio: 'inherit' });
server.on('exit', (code) => process.exit(code ?? 0));
for (const signal of ['SIGINT', 'SIGTERM']) {
    process.on(signal, () => server.kill(signal));
}
