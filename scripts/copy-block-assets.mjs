import { copyFile, mkdir } from 'node:fs/promises';
import { resolve } from 'node:path';

const root = process.cwd();
const sourceDirectory = resolve(root, 'blocks/resource-explorer');
const targetDirectory = resolve(root, 'build/resource-explorer');

await mkdir(targetDirectory, { recursive: true });
await Promise.all([
    copyFile(resolve(sourceDirectory, 'block.json'), resolve(targetDirectory, 'block.json')),
    copyFile(resolve(sourceDirectory, 'view.js'), resolve(targetDirectory, 'view.js'))
]);

console.log('Block metadata and frontend script copied successfully.');