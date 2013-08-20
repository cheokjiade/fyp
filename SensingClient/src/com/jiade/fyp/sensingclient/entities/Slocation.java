package com.jiade.fyp.sensingclient.entities;

import java.util.Date;

public class Slocation {
	
	private String locationLat;
	private String locationLng;
	private String locationAlt;
	private float locationAcc;
	private Date locationTimeStamp;
	private SSMS objSMS;
	
	public Slocation(String locationLat, String locationLng,
			String locationAlt, float locationAcc, Date locationTimeStamp) {
		super();
		this.locationLat = locationLat;
		this.locationLng = locationLng;
		this.locationAlt = locationAlt;
		this.locationAcc = locationAcc;
		this.locationTimeStamp = locationTimeStamp;
	}
	
	public String getLocationLat() {
		return locationLat;
	}
	public void setLocationLat(String locationLat) {
		this.locationLat = locationLat;
	}
	public String getLocationLng() {
		return locationLng;
	}
	public void setLocationLng(String locationLng) {
		this.locationLng = locationLng;
	}
	public String getLocationAlt() {
		return locationAlt;
	}
	public void setLocationAlt(String locationAlt) {
		this.locationAlt = locationAlt;
	}
	public float getLocationAcc() {
		return locationAcc;
	}
	public void setLocationAcc(float locationAcc) {
		this.locationAcc = locationAcc;
	}
	public Date getLocationTimeStamp() {
		return locationTimeStamp;
	}
	public void setLocationTimeStamp(Date locationTimeStamp) {
		this.locationTimeStamp = locationTimeStamp;
	}
	
	
}
