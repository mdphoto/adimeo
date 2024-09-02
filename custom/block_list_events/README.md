# Block list events

Ce module permet de lister les événements à venir.
Il crée un bloc personnalisé (plugin annoté) qui s'affiche sur la page de détail d'un événement.
Ce bloc affiche 3 autres événements du même type (taxonomie) que l'événement courant,
ordonnés par date de début (ascendant) et dont la date de fin n'est pas dépassée.

S'il y a moins de 3 événements du même type, le module complète avec un ou plusieurs événements d'autres types,
également ordonnés par date de début (ascendant) et dont la date de fin n'est pas dépassée.

Pour sélectionner des événements d'autres taxonomies,
le module exclut le terme de l'événement en cours jusqu'à ce que les 3 événements soient trouvés.

Je vérifie toutes les dates de fin des événements pour m'assurer que seuls les événements à venir sont affichés.

## Installation
Grâce au fichier de configuration, le bloc est déjà activé et configuré pour s'afficher sur la page de détail d'un événement.
