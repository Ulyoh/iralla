select id, bl_id, lat, lng, previous_link_id, next_link_id, flows
from squares
where lat between ? and ?
and lng between ? and ?
order by id