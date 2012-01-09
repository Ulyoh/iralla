CREATE TEMPORARY TABLE IF NOT EXISTS t_temp1
SELECT * FROM
(
	SELECT @row1 := @row1 +1 AS row , l1.* 
	FROM  (
	   SELECT * FROM links
	   ORDER BY `busLineId`, `prevIndex`,`distanceToPrevIndex`
	) l1,
	(SELECT @row1 := 0 )r 
) AS t;

CREATE TEMPORARY TABLE IF NOT EXISTS t_temp2
SELECT * FROM
	(
	SELECT @row2 := @row2 +1 AS row , l2.* 
		FROM  (
		   SELECT * FROM links
		   ORDER BY `busLineId`, `prevIndex`,`distanceToPrevIndex`
		) l2,
		
		(SELECT @row2 :=0)r 
	) AS t;

CREATE TABLE IF NOT EXISTS links_couples
SELECT	t_temp1.row AS row1,
		t_temp1.id AS id1,
		t_temp1.busStationId AS busStationId1,
		t_temp1.busLineID AS busLineId1,
		t_temp1.prevIndex AS prevIndexId1,
		t_temp1.distanceToPrevIndex AS distanceToPrevIndex1,
		t_temp1.lat AS lat1,
		t_temp1.lng AS lng1,
		t_temp2.row AS row2,
		t_temp2.id AS id2,
		t_temp2.busStationId AS busStationId2,
		t_temp2.busLineId AS busLineId2,
		t_temp2.prevIndex AS prevIndexId2,
		t_temp2.distanceToPrevIndex AS distanceToPrevIndex2,
		t_temp2.lat AS lat2,
		t_temp2.lng AS lng2
FROM t_temp1
JOIN t_temp2 ON (t_temp1.row = t_temp2.row+1) AND (t_temp1.busLineId = t_temp2.busLineId)
ORDER BY busStationId1, busStationId2;

ALTER TABLE links_couples ADD id MEDIUMINT(6) UNSIGNED PRIMARY KEY AUTO_INCREMENT FIRST;

CREATE INDEX busStationCouple1 ON links_couples (busStationId1, busStationId2);
CREATE INDEX busStationCouple2 ON links_couples (busStationId2, busStationId1);

CREATE TABLE bus_stations_couples_path
SELECT busStationId1, busStationId2 FROM links_couples
GROUP BY busStationId1, busStationId2;

ALTER TABLE bus_stations_couples_path 	
	ADD id 		MEDIUMINT(6) UNSIGNED PRIMARY KEY AUTO_INCREMENT FIRST,
	ADD path 	TEXT;