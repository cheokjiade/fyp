<?php
/**
 * Created by IntelliJ IDEA.
 * User: Me
 * Date: 3/19/14
 * Time: 4:46 PM
 * To change this template use File | Settings | File Templates.
 */

/* This gets the stop points, and the best prediction of what the next stop point will be and the actual stop point
 *
SELECT * FROM
	(SELECT * FROM
		(SELECT * FROM fyp.stoppoint WHERE session_hash = 'ff5d81d3b3c1034d3d722fbd3a037bab0e536887c4c122afc375502b1075fbac76e0c8e74dc1ede0b6e0ab9894153b62bc2c49a887d3f6c9982e09f3df801ce3' LIMIT 0, 150) sp1
	JOIN
		(SELECT locationpoint_from_id, locationpoint_to_id, MAX(locationvariantprob_count) FROM fyp.locationvariantprob WHERE session_hash = 'ff5d81d3b3c1034d3d722fbd3a037bab0e536887c4c122afc375502b1075fbac76e0c8e74dc1ede0b6e0ab9894153b62bc2c49a887d3f6c9982e09f3df801ce3' GROUP BY session_hash, locationpoint_from_id)lvp1
	ON sp1.locationpoint_id = lvp1.locationpoint_from_id) prediction
JOIN
	(SELECT r.stoppoint_id_start, r.stoppoint_id_end, sp2.locationpoint_id FROM
		route r
	JOIN
		stoppoint sp2
	ON r.stoppoint_id_end = sp2.stoppoint_id)destination
ON prediction.stoppoint_id = destination.stoppoint_id_start AND prediction.locationpoint_to_id = destination.locationpoint_id
 */

/* get the top 3 predictions
 *  SELECT * FROM
	(SELECT * FROM
		(SELECT * FROM fyp.stoppoint WHERE session_hash = 'ff5d81d3b3c1034d3d722fbd3a037bab0e536887c4c122afc375502b1075fbac76e0c8e74dc1ede0b6e0ab9894153b62bc2c49a887d3f6c9982e09f3df801ce3' LIMIT 500, 50) sp1
	JOIN
		(SELECT locationpoint_from_id, locationpoint_to_id, locationvariantprob_count FROM locationvariantprob lvp1 WHERE (SELECT COUNT(*) FROM locationvariantprob lvp2 WHERE lvp1.locationpoint_from_id = lvp2.locationpoint_from_id AND lvp2.locationvariantprob_count >= lvp1.locationvariantprob_count)<=3 AND lvp1.session_hash = 'ff5d81d3b3c1034d3d722fbd3a037bab0e536887c4c122afc375502b1075fbac76e0c8e74dc1ede0b6e0ab9894153b62bc2c49a887d3f6c9982e09f3df801ce3')lvp3
	ON sp1.locationpoint_id = lvp3.locationpoint_from_id) prediction
JOIN
	(SELECT r.stoppoint_id_start, r.stoppoint_id_end, sp2.locationpoint_id FROM
		route r
	JOIN
		stoppoint sp2
	ON r.stoppoint_id_end = sp2.stoppoint_id)destination
ON prediction.stoppoint_id = destination.stoppoint_id_start AND prediction.locationpoint_to_id = destination.locationpoint_id
 */