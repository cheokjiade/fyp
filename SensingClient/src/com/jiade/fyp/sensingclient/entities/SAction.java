package com.jiade.fyp.sensingclient.entities;

public class SAction {
	public static final int SCREEN_ON_OFF = 1, DATA_CONNECTION = 2;
	public static final int SCREEN_ON = 7, SCREEN_OFF = 8;
	public static final int WIFI_CONNECTED = 2, MOBILE_CONNECTED = 1, BOTH_CONNECTED = 3;
	int actionSource;
	int actionAction;
	/**
	 * @param actionSource
	 * @param actionAction
	 */
	public SAction(int actionSource, int actionAction) {
		super();
		this.actionSource = actionSource;
		this.actionAction = actionAction;
	}
	/**
	 * @return the actionSource
	 */
	public int getActionSource() {
		return actionSource;
	}
	/**
	 * @param actionSource the actionSource to set
	 */
	public void setActionSource(int actionSource) {
		this.actionSource = actionSource;
	}
	/**
	 * @return the actionAction
	 */
	public int getActionAction() {
		return actionAction;
	}
	/**
	 * @param actionAction the actionAction to set
	 */
	public void setActionAction(int actionAction) {
		this.actionAction = actionAction;
	}
	
}
