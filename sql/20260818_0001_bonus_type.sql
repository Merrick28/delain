-- Ajout de la colonne pour mémoriser la valeur de l'aura à sa création
-- (sert de référence pour calculer le % d'état affiché au joueur)
ALTER TABLE public.bonus ADD COLUMN bonus_valeur_initiale numeric;