select 
	id, 
	bl_id, 
	lat, 
	lng, 
	prev_index_of_next_link, 
	prev_index_of_prev_link, 
	flows, 
	prev_index_of_pt,
	distance_from_first_vertex,
	pt_coords_lat,
	pt_coords_lng
from squares
where lat between ? and ?
and lng between ? and ?
order by bl_id, id


		