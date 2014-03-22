<?php
/**
 * Created by IntelliJ IDEA.
 * User: Me
 * Date: 3/22/14
 * Time: 11:39 AM
 * To change this template use File | Settings | File Templates.
 */
/*
 * Find the location points that are transportation related
 * SELECT * FROM locationtag lt, locationpoint lp, locationandtags lat WHERE (lt.locationtag_text = 'bus_station' OR lt.locationtag_text = 'transit_station' OR lt.locationtag_text = 'airport' OR lt.locationtag_text = 'subway_station' OR lt.locationtag_text = 'train_station') AND lt.locationtag_id = lat.locationtag_id AND lat.locationpoint_id = lp.locationpoint_id;
 *
 *
SELECT *, SUM(overlaptime), COUNT(overlaptime) FROM (
	SELECT
			sp1.locationpoint_id,
			sp1.session_hash AS session_hash_1,
			sp1.stoppoint_start_time AS stoppoint_start_time_1,
			sp1.stoppoint_end_time AS stoppoint_end_time_1,
			sp2.session_hash  AS session_hash_2,
			sp2.stoppoint_start_time AS stoppoint_start_time_2,
			sp2.stoppoint_end_time AS stoppoint_end_time_2,
			CASE WHEN (sp1.stoppoint_start_time > sp2.stoppoint_start_time) THEN (sp1.stoppoint_start_time) ELSE (sp2.stoppoint_start_time) END AS overlapstart,
			CASE WHEN (sp1.stoppoint_end_time < sp2.stoppoint_end_time) THEN (sp1.stoppoint_end_time) ELSE (sp2.stoppoint_end_time) END AS overlapend,
			TIMESTAMPDIFF(MINUTE,
			CASE WHEN (sp1.stoppoint_start_time > sp2.stoppoint_start_time) THEN (sp1.stoppoint_start_time) ELSE (sp2.stoppoint_start_time) END,
			CASE WHEN (sp1.stoppoint_end_time < sp2.stoppoint_end_time) THEN (sp1.stoppoint_end_time) ELSE (sp2.stoppoint_end_time) END )
			AS overlaptime
	FROM	stoppoint sp1, stoppoint sp2
	WHERE	((sp1.stoppoint_start_time BETWEEN sp2.stoppoint_start_time AND sp2.stoppoint_end_time
			OR sp2.stoppoint_start_time BETWEEN sp1.stoppoint_start_time AND sp1.stoppoint_end_time)
			AND sp1.session_hash <> sp2.session_hash
			AND sp1.locationpoint_id = sp2.locationpoint_id
			AND sp1.stoppoint_id > sp2.stoppoint_id)) overlap
WHERE overlap.overlaptime > 30
GROUP BY locationpoint_id, session_hash_1, session_hash_2
 */