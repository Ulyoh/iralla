-- select squares around start point:
(
	select id,lat,lng,bl_id from squares
	where lat between ? and ?
	and lng between ? and ?
) squares_start_all

-- select squares around end point:
(
	select id,lat,lng,bl_id from squares
	where lat between ? and ?
	and lng between ? and ?
) squares_end_all

-- select commun bl_id of squares_start and squares_end
(
	select squares_start_all.bl_id and squares_end_all.bl_id 
	from squares_start_all, squares_end_all
	group by bl_i_d
)
