select id, bl_id, lat, lng, prev_index_of_next_link, prev_index_of_prev_link, flows
from squares
where lat between ? and ?
and lng between ? and ?
order by bl_id, id