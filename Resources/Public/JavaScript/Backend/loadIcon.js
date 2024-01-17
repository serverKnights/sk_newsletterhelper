import RegularEvent from '@typo3/core/event/regular-event.js';

new RegularEvent('submit', function (e) {
    var loadingIcon = document.getElementById('loadingOverlay');
    loadingIcon.style.display = 'flex';
}).bindTo(document.querySelector('#mjmlInstallForm'));