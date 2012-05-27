-- create temporary table around start point:
create table start_bls
select bl_id, lat, lng,
MIN(previous_link_id) as start_min_previous_link_id,
MAX(previous_link_id) as start_max_previous_link_id,
MIN(next_link_id) as start_min_next_link_id,
MAX(next_link_id) as start_max_next_link_id,
flows
from squares
where lat between ? and ?
and lng between ? and ?
group by bl_id
-- index bl_id

--select * from toto t1 where id not in (select id from toto where id + 1 = t1.id)    MIN(id), MAX(id),
--create table start_bls
--select id, bl_id, lat, lng,
--previous_link_id,
--previous_link_id,
--next_link_id,
--next_link_id,
--flows
--from squares
--where lat between ? and ?
--and lng between ? and ?
--order by id


