-- create temporary table around end point:
create table end_bls
select bl_id, lat, lng,
MIN(previous_link_id) as end_min_previous_link_id,
MAX(previous_link_id) as end_max_previous_link_id,
MIN(next_link_id) as end_min_next_link_id,
MAX(next_link_id) as end_max_next_link_id,
flows
from squares
where lat between ? and ?
and lng between ? and ?
group by bl_id
-- index bl_id