package com.jiade.fyp.sensingclient.entities;

import java.util.Date;

public class Slocation {
	
	private String locationLat;
	private String locationLng;
	private String locationAlt;
	private float locationAcc;
	private Date locationTimeStamp;
	private SSMS objSMS;
	private SCall objCall;
	private SActivity objActivity;
	
	
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

	/**
	 * @return the objSMS
	 */
	public SSMS getObjSMS() {
		return objSMS;
	}

	/**
	 * @param objSMS the objSMS to set
	 */
	public void setObjSMS(SSMS objSMS) {
		this.objSMS = objSMS;
	}

	public SCall getObjCall() {
		return objCall;
	}

	public void setObjCall(SCall objCall) {
		this.objCall = objCall;
	}

	public SActivity getObjActivity() {
		return objActivity;
	}

	public void setObjActivity(SActivity objActivity) {
		this.objActivity = objActivity;
	}
	
	
}
