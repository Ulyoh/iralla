-- select commun bl_id of squares_start and squares_end

select
	start_bls.bl_id as start_bl_id,
	start_bls.previous_link_id as start_previous_link_id,
	start_bls.next_link_id as start_next_link_id,
	end_bls.bl_id as end_bl_id,
	end_bls.previous_link_id as end_previous_link_id,
	end_bls.next_link_id as end_next_link_id
from start_bls
inner join end_bls	
on start_bls.bl_id = end_bls.bl_id