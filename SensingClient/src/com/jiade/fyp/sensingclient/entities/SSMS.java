package com.jiade.fyp.sensingclient.entities;

public class SSMS {
	/**
	 * 0 = Incomming
	 * 1 = Outgoing
	 */
	private int SMSIO;
	/**
	 * The Phone Number
	 */
	private String SMSNumber;
	
	public SSMS(int sMSIO, String sMSNumber) {
		super();
		SMSIO = sMSIO;
		SMSNumber = sMSNumber;
	}
	/**
	 * @return the sMSIO
	 */
	public int getSMSIO() {
		return SMSIO;
	}
	/**
	 * @param sMSIO the sMSIO to set
	 */
	public void setSMSIO(int sMSIO) {
		SMSIO = sMSIO;
	}
	/**
	 * @return the sMSNumber
	 */
	public String getSMSNumber() {
		return SMSNumber;
	}
	/**
	 * @param sMSNumber the sMSNumber to set
	 */
	public void setSMSNumber(String sMSNumber) {
		SMSNumber = sMSNumber;
	}
	
	
}
