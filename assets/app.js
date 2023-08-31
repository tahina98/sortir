/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';
    window.onload = (event) => {
    annulation();
};


    function annulation(){

    const sorties = document.getElementById('sorties');


        if (sorties) {
            sorties.addEventListener('click', e => {
                if (e.target.className === 'annulation') {
                    let id = e.target.getAttribute('data-id');
                    let motif = prompt('Merci de saisir un motif d\'annulation');

                    console.log(id);
                    console.log(motif);
                    fetch('http://127.0.0.1:8000/sorties/annulation/' + id + '/' + motif).then((r) =>
                        window.location.replace('http://127.0.0.1:8000/sorties/'));
                }
            })
        }


    }

