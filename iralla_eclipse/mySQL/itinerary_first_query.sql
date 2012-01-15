-- create temporary table around start point:
create temporary table squares_start
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
index bl_id;

-- create temporary table around end point:
create temporary table squares_end
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
index bl_id;

-- select commun bl_id of squares_start and squares_end
(
	select squares_start.bl_id and squares_end.bl_id 
	from squares_start, squares_end
	group by bl_i_d
)

