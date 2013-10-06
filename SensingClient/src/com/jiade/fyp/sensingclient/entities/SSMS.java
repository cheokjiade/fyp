package com.jiade.fyp.sensingclient.entities;
/**
 * int SMSIO
 * 0 = Incomming
 * 1 = Outgoing
 */
public class SSMS {
	
	public static final int INCOMMING = 0;
	public static final int OUTOGOING = 1;
	public static final int IS_ADVERTISMENT = 1;
	public static final int IS_NOT_ADVERTISMENT = 0;
	/**
	 * 0 = Incomming
	 * 1 = Outgoing
	 */
	private int SMSIO;
	/**
	 * The Phone Number
	 */
	private String SMSNumber;
	private int SMSisADV;
	private int SMSlength;
	
	/**
	 * @param sMSIO
	 * @param sMSNumber
	 * @param sMSisADV
	 * @param sMSlength
	 */
	public SSMS(int sMSIO, String sMSNumber, int sMSisADV, int sMSlength) {
		super();
		SMSIO = sMSIO;
		SMSNumber = sMSNumber;
		SMSisADV = sMSisADV;
		SMSlength = sMSlength;
	}

	/**
	 * @param sMSIO
	 * @param sMSNumber
	 * @param sMSisADV
	 */
	public SSMS(int sMSIO, String sMSNumber, int sMSisADV) {
		super();
		SMSIO = sMSIO;
		SMSNumber = sMSNumber;
		SMSisADV = sMSisADV;
	}
	
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
	/**
	 * @return the sMSisADV
	 */
	public int isSMSisADV() {
		return SMSisADV;
	}
	/**
	 * @param sMSisADV the sMSisADV to set
	 */
	public void setSMSisADV(int sMSisADV) {
		SMSisADV = sMSisADV;
	}

	/**
	 * @return the sMSlength
	 */
	public int getSMSlength() {
		return SMSlength;
	}

	/**
	 * @param sMSlength the sMSlength to set
	 */
	public void setSMSlength(int sMSlength) {
		SMSlength = sMSlength;
	}
	
	
}
