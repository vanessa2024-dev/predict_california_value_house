<?php

echo "
                <div id='message-erreur' >
                    veuillez tout d'abord valider votre commande..
                </div>

                <style>
                    #message-erreur 
                    {
                        position: fixed;           /* Le message reste visible en haut à droite */
                        top: 20px;
                        right: 20px;
                        background-color: #fa0d0dff; /* rouge pour echec */
                        color: white;
                        padding: 15px 25px;
                        border-radius: 8px;
                        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
                        font-family: Arial, sans-serif;
                        font-size: 16px;
                        z-index: 1000;
                        opacity: 1;                /* Pleinement visible */
                        transition: opacity 0.5s ease-in-out; /* Pour une disparition douce */
                    }
                </style>
                <script>
                    setTimeout(function() {
                        var message = document.getElementById('message-erreur');
                        message.style.opacity = '0';   // Commence à disparaître
                        setTimeout(function() {
                            message.remove();           // Supprime complètement du DOM après la transition
                        }, 500);                        // 500ms = durée de la transition
                    }, 3000);                            // 3000ms = 3 secondes avant disparition
                </script>";



?>