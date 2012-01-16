-- select commun bl_id of squares_start and squares_end

select
	start_bls.bl_id as start_bl_id,
	start_bls.start_min_previous_link_id,
	start_bls.start_max_previous_link_id,
	start_bls.start_min_next_link_id,
	start_bls.start_max_next_link_id,
	end_bls.bl_id as end_bl_id,
	end_bls.end_min_previous_link_id,
	end_bls.end_max_previous_link_id,
	end_bls.end_min_next_link_id,
	end_bls.end_max_next_link_id
from start_bls
inner join end_bls	
on start_bls.bl_id = end_bls.bl_id