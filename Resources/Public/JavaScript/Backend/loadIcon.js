import RegularEvent from '@typo3/core/event/regular-event.js';

var install_form = document.querySelector('#sk_mjmlInstallForm')
if(install_form != null){
    new RegularEvent('submit', function (e) {
        var loadingIcon = document.getElementById('sk_loadingOverlay');
        loadingIcon.style.display = 'flex';
    }).bindTo(install_form);
}
