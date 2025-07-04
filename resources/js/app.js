import './bootstrap';

import Alpine from 'alpinejs';

console.log('Alpine.js está sendo importado');

window.Alpine = Alpine;

console.log('Inicializando Alpine.js...');
Alpine.start();
console.log('Alpine.js foi inicializado com sucesso!', window.Alpine);
