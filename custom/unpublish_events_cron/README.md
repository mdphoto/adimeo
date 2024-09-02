# Unpublish Events Cron

Ce module permet de dépublier les événements passés en utilisant un QueueWorker.
Il récupère la date actuelle et la compare avec la date de fin de chaque événement à l'aide du champ datetime_range.
Si la date de fin est dépassée, l'événement est dépublié.
