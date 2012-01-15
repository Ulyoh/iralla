-- create temporary table around start point:
create table start_bls
engine memory
select bl_id, lat, lng, 
-- prev_index_of_prev_link, prev_index_of_next_link,
-- prev_link_coords_lat, prev_link_coords_lng, next_link_coords_lat,
-- next_link_coords_lng, prev_bs_linked_id, next_bs_linked_id,
previous_link_id, next_link_id, flows
from squares
where lat between ? and ?
and lng between ? and ?
group by previous_link_id, next_link_id
-- index bl_id





