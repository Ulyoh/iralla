select start_bls.start_previous_link_id,
	start_bls.start_next_link_id,
	start_bls.start_flows ,
	links.busLineId as start_busLineId,
	links.busStationId as start_busStationId,
	links.id as start_id
from start_bls
inner join links on links.busLineId = start_bls.bl_id
inner join (
	select end_bls.end_previous_link_id,
	end_bls.end_next_link_id,
	end_bls.end_flows ,
	links.busLineId as end_busLineId,
	links.busStationId as end_busStationId,
	links.id as end_id
	from end_bls
	inner join links on links.busLineId = end_bls.bl_id
)end_links
on links.busStationId = end_links.end_busStationId