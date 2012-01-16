-- create temporary table around start point:
create table start_bls
select bl_id, lat, lng,
MIN(previous_link_id) as start_min_previous_link_id,
MAX(previous_link_id) as start_max_previous_link_id,
MIN(next_link_id) as start_min_next_link_id,
MAX(next_link_id) as start_max_next_link_id,
flows
from squares
where lat between ? and ?
and lng between ? and ?
group by bl_id
-- index bl_id





