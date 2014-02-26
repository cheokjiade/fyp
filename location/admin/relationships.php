<?php
/**
 * Created by IntelliJ IDEA.
 * User: Me
 * Date: 2/24/14
 * Time: 4:41 PM
 * To change this template use File | Settings | File Templates.
 */

/*SELECT
    	sp1.locationpoint_id,
		sp1.session_hash,
		sp1.stoppoint_start_time,
		sp1.stoppoint_end_time,
		sp2.session_hash,
		sp2.stoppoint_start_time,
		sp2.stoppoint_end_time,
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
		AND sp1.stoppoint_id > sp2.stoppoint_id) */
