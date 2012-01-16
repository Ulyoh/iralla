select start_bls.start_previous_link_id,
	start_bls.start_next_link_id,
	start_bls.start_flows ,
	l1.busLineId as start_busLineId,
	l1.busStationId as start_busStationId,
	l1.id as start_id,
	l2.busLineId as start_inter_busLineId,
	l2.busStationId as start_inter_busStationId
from start_bls
inner join links as l1 on l1.busLineId = start_bls.bl_id
inner join links as l2 on l2.busStationId = l1.bus_stations.id
inner join (
	select end_bls.end_previous_link_id,
	end_bls.end_next_link_id,
	end_bls.end_flows ,
	l3.busLineId as end_busLineId,
	l3.busStationId as end_busStationId,
	l4.id as start_id,
	l4.busLineId as end_inter_busLineId,
	l4.busStationId as end_inter_busStationId
	from end_bls
	inner join links as l3 on l3.busLineId = end_bls.bl_id
	inner join links as l4 on l4.busStationId = l3.bus_stations.id
)end_links
on links.start_inter_busLineId = end_links.end_inter_busLineId
or links.start_inter_busLineId < 23
and end_links.end_inter_busLineId < 23