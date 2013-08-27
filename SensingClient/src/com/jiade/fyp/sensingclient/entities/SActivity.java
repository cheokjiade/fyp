package com.jiade.fyp.sensingclient.entities;

public class SActivity {
	
	public static final int IN_VEHICLE = 0, ON_BICYCLE = 1, ON_FOOT = 2, STILL = 3, TILTING = 5, UNKNOWN= 4;
	
	private int activityProbableActivity;
	private int activityProbableActivityConfidence;
	private int activitySecondProbableActivity;
	private int activitySecondProbableActivityConfidence;
	public SActivity(int activityProbableActivity,
			int activityProbableActivityConfidence,
			int activitySecondProbableActivity,
			int activitySecondProbableActivityConfidence) {
		super();
		this.activityProbableActivity = activityProbableActivity;
		this.activityProbableActivityConfidence = activityProbableActivityConfidence;
		this.activitySecondProbableActivity = activitySecondProbableActivity;
		this.activitySecondProbableActivityConfidence = activitySecondProbableActivityConfidence;
	}
	/**
	 * @return the activityProbableActivity
	 */
	public int getActivityProbableActivity() {
		return activityProbableActivity;
	}
	/**
	 * @param activityProbableActivity the activityProbableActivity to set
	 */
	public void setActivityProbableActivity(int activityProbableActivity) {
		this.activityProbableActivity = activityProbableActivity;
	}
	/**
	 * @return the activityProbableActivityConfidence
	 */
	public int getActivityProbableActivityConfidence() {
		return activityProbableActivityConfidence;
	}
	/**
	 * @param activityProbableActivityConfidence the activityProbableActivityConfidence to set
	 */
	public void setActivityProbableActivityConfidence(
			int activityProbableActivityConfidence) {
		this.activityProbableActivityConfidence = activityProbableActivityConfidence;
	}
	/**
	 * @return the activitySecondProbableActivity
	 */
	public int getActivitySecondProbableActivity() {
		return activitySecondProbableActivity;
	}
	/**
	 * @param activitySecondProbableActivity the activitySecondProbableActivity to set
	 */
	public void setActivitySecondProbableActivity(int activitySecondProbableActivity) {
		this.activitySecondProbableActivity = activitySecondProbableActivity;
	}
	/**
	 * @return the activitySecondProbableActivityConfidence
	 */
	public int getActivitySecondProbableActivityConfidence() {
		return activitySecondProbableActivityConfidence;
	}
	/**
	 * @param activitySecondProbableActivityConfidence the activitySecondProbableActivityConfidence to set
	 */
	public void setActivitySecondProbableActivityConfidence(
			int activitySecondProbableActivityConfidence) {
		this.activitySecondProbableActivityConfidence = activitySecondProbableActivityConfidence;
	}
	
	
}
