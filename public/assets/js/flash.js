
document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.flash').forEach(flash => {

        setTimeout(() => {

            flash.style.transition = 'opacity .3s ease';

            flash.style.opacity = '0';

            setTimeout(() => flash.remove(), 300);

        }, 5000);

    });

});