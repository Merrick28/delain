-- auto-generated definition
create table distance
(
    distance_pos1     integer,
    distance_pos2     integer,
    distance_etage    integer,
    distance_distance integer
)
    partition by list (distance_etage);

comment on table distance is 'Distance entre deux positions (table partitionnée)';

alter table distance
    owner to delain;