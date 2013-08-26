package com.jiade.fyp.sensingclient.entities;

public class SCall {
	public static final int INCOMMING = 0;
	public static final int OUTOGOING = 1;
	private int callIo;
	private String callNo;
	private long callDuration;
	public SCall(int callIo, String callNo, long callDuration) {
		super();
		this.callIo = callIo;
		this.callNo = callNo;
		this.callDuration = callDuration;
	}
	public int getCallIo() {
		return callIo;
	}
	public void setCallIo(int callIo) {
		this.callIo = callIo;
	}
	public String getCallNo() {
		return callNo;
	}
	public void setCallNo(String callNo) {
		this.callNo = callNo;
	}
	public long getCallDuration() {
		return callDuration;
	}
	public void setCallDuration(long callDuration) {
		this.callDuration = callDuration;
	}
	
}
