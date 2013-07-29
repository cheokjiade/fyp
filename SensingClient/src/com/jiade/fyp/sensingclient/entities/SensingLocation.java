package com.jiade.fyp.sensingclient.entities;

import org.json.JSONException;
import org.json.JSONObject;

public class SensingLocation {

	private long location_id;
	private long location_time;
	private String location_lat;
	private String location_lng;
	private String location_alt;
	private String location_speed;
	private String location_accuracy;
	public long getLocation_id() {
		return location_id;
	}
	public void setLocation_id(long location_id) {
		this.location_id = location_id;
	}
	public long getLocation_time() {
		return location_time;
	}
	public void setLocation_time(long location_time) {
		this.location_time = location_time;
	}
	public String getLocation_lat() {
		return location_lat;
	}
	public void setLocation_lat(String location_lat) {
		this.location_lat = location_lat;
	}
	public String getLocation_lng() {
		return location_lng;
	}
	public void setLocation_lng(String location_lng) {
		this.location_lng = location_lng;
	}
	public String getLocation_alt() {
		return location_alt;
	}
	public void setLocation_alt(String location_alt) {
		this.location_alt = location_alt;
	}
	public String getLocation_speed() {
		return location_speed;
	}
	public void setLocation_speed(String location_speed) {
		this.location_speed = location_speed;
	}
	public String getLocation_accuracy() {
		return location_accuracy;
	}
	public void setLocation_accuracy(String location_accuracy) {
		this.location_accuracy = location_accuracy;
	}
	public SensingLocation(long location_id, long location_time,
			String location_lat, String location_lng, String location_alt,
			String location_speed, String location_accuracy) {
		super();
		this.location_id = location_id;
		this.location_time = location_time;
		this.location_lat = location_lat;
		this.location_lng = location_lng;
		this.location_alt = location_alt;
		this.location_speed = location_speed;
		this.location_accuracy = location_accuracy;
	}
	public JSONObject toJSONObject(){
		JSONObject obj = new JSONObject();
		try{
			//obj.put("location_id", location_id);
			obj.put("location_time", location_time);
			obj.put("location_lat", location_lat);
			obj.put("location_lng", location_lng);
			obj.put("location_alt", location_alt);
			obj.put("location_speed", location_speed);
			obj.put("location_accuracy", location_accuracy);
		}catch(JSONException e){
			e.printStackTrace();
		}
		return obj;
	}
}
