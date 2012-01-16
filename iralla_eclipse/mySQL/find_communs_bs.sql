select * from (
select start_bls.start_min_previous_link_id,
	start_bls.start_max_previous_link_id,
	start_bls.start_min_next_link_id,
	start_bls.start_max_next_link_id,
	links.busLineId as start_busLineId,
	links.busStationId as start_busStationId,
	links.id as start_id,
	end_links.end_min_previous_link_id,
	end_links.end_max_previous_link_id,
	end_links.end_min_next_link_id,
	end_links.end_max_next_link_id,
	end_links.flows,
	end_links.end_busLineId,
	end_links.end_busStationId
from start_bls
inner join links on links.busLineId = start_bls.bl_id
inner join (
	select end_bls.end_min_previous_link_id,
	end_bls.end_max_previous_link_id,
end_bls.end_min_next_link_id,
	end_bls.end_max_next_link_id,
	end_bls.flows ,
	links.id as start_id,
	links.busLineId as end_busLineId,
	links.busStationId as end_busStationId
	from end_bls
	inner join links on links.busLineId = end_bls.bl_id
)end_links
on links.busStationId = end_links.end_busStationId
) result
group by result.start_busLineId