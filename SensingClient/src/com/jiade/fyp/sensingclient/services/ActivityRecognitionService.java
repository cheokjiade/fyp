package com.jiade.fyp.sensingclient.services;

import java.util.Date;

import com.google.android.gms.common.ConnectionResult;
import com.google.android.gms.common.GooglePlayServicesClient.ConnectionCallbacks;
import com.google.android.gms.common.GooglePlayServicesClient.OnConnectionFailedListener;
import com.google.android.gms.common.GooglePlayServicesUtil;
import com.google.android.gms.location.ActivityRecognitionClient;
import com.google.android.gms.location.ActivityRecognitionResult;
import com.google.android.gms.location.DetectedActivity;
import com.jiade.fyp.sensingclient.db.Db4oHelper;
import com.jiade.fyp.sensingclient.entities.SActivity;
import com.jiade.fyp.sensingclient.entities.SSMS;
import com.jiade.fyp.sensingclient.entities.Slocation;
import com.nullwire.trace.ExceptionHandler;

import android.app.Dialog;
import android.app.IntentService;
import android.app.PendingIntent;
import android.app.Service;
import android.content.Intent;
import android.location.Location;
import android.os.Bundle;
import android.os.IBinder;
import android.util.Log;
import android.widget.Toast;

public class ActivityRecognitionService extends IntentService implements ConnectionCallbacks, OnConnectionFailedListener{

	private static SActivity tempActivity;
	// Flag that indicates if a request is underway.
    private boolean mInProgress;
    
	// Stores the PendingIntent used to send activity recognition events back to the app
    private PendingIntent mActivityRecognitionPendingIntent;

    // Stores the current instantiation of the activity recognition client
    private ActivityRecognitionClient mActivityRecognitionClient;
    
    private static int notMoving= 0;
    private static boolean still=false;
    
	public ActivityRecognitionService() {
		super("TestSensingActivityRecognition");
	}

	@Override
	public void onConnectionFailed(ConnectionResult arg0) {
		Log.w("ActivityRecognitionService", "Connection Failed");
		
	}

	@Override
	public void onConnected(Bundle arg0) {
		Log.w("ActivityRecognitionService", "Connected");
		mActivityRecognitionClient.requestActivityUpdates(4000, mActivityRecognitionPendingIntent);
		
	}

	@Override
	public void onDisconnected() {
		Log.w("ActivityRecognitionService", "Disconnected");
		mActivityRecognitionClient.connect();
		
	}

	@Override
	protected void onHandleIntent(Intent intent) {
		if (ActivityRecognitionResult.hasResult(intent)) {
	         ActivityRecognitionResult result = ActivityRecognitionResult.extractResult(intent);
	         int i= result.getMostProbableActivity().getType();
	         Log.w("ActivityRecognitionService", getNameFromType(i) +  Integer.toString(result.getActivityConfidence(i)));
	         //Toast.makeText(getApplicationContext(), "ActivityRecognitionService" + getNameFromType(i), Toast.LENGTH_SHORT).show();
	         tempActivity = new SActivity(result.getMostProbableActivity().getType(), result.getMostProbableActivity().getConfidence(), 0, 0);
	         if(SensingService.lastKnownLocation!=null){
	        	 //Location lastKnownLocation = SensingService.lastKnownLocation;
	             //Slocation loc = new Slocation(Double.toString(lastKnownLocation.getLatitude()), Double.toString(lastKnownLocation.getLongitude()), Double.toString(lastKnownLocation.getAltitude()), lastKnownLocation.getAccuracy(), new Date());
	             //loc.setObjActivity(new SActivity(result.getMostProbableActivity().getType(), result.getMostProbableActivity().getConfidence(), 0, 0));
	             //Db4oHelper.getInstance(getApplicationContext()).db().store(loc);
	             //Db4oHelper.getInstance(getApplicationContext()).db().close();
	         }
	         //Toast.makeText(getApplicationContext(), (result.getMostProbableActivity().getType()==DetectedActivity.STILL)?"Still":"Not Still", Toast.LENGTH_SHORT).show();
	         if(result.getMostProbableActivity().getType() == DetectedActivity.STILL && result.getMostProbableActivity().getConfidence()>70){
	        	 notMoving++;
	        	 still = true;
	         }else {
	        	 still = false;
	        	 notMoving = 0;
	        	 if(SensingService.ss!=null&&SensingService.ss.getUpdateInterval()!=15000){
						//SensingService.ss.setUpdateInterval(15000);
						//SensingService.ss.setInterval(15000);
					}
	         }
	         
	         if(notMoving>=8&&notMoving<40){
	        	 if(SensingService.ss!=null&&SensingService.ss.getUpdateInterval()!=30000){
		        	 //SensingService.ss.setInterval(30000);
		        	 //SensingService.ss.setUpdateInterval(30000);
		         }
	         }else if(notMoving>=40){
	        	 if(SensingService.ss!=null&&SensingService.ss.getUpdateInterval()!=600000){
		        	 //SensingService.ss.setInterval(600000);
		        	 //SensingService.ss.setUpdateInterval(600000);
		         }
	         }
	     }
		
	}
	private boolean servicesConnected() {
        // Check that Google Play services is available
        int resultCode =
                GooglePlayServicesUtil.
                        isGooglePlayServicesAvailable(this);
        // If Google Play services is available
        if (ConnectionResult.SUCCESS == resultCode) {
            // In debug mode, log the status
            Log.w("Activity Recognition",
                    "Google Play services is available.");
            // Continue
            return true;
        // Google Play services was not available for some reason
        } else {
            // Get the error dialog from Google Play services
            
            return false;
        }
    }

	@Override
	public void onCreate() {
		/*
         * Instantiate a new activity recognition client. Since the
         * parent Activity implements the connection listener and
         * connection failure listener, the constructor uses "this"
         * to specify the values of those parameters.
         */
		//ExceptionHandler.register(this, "http://fyp.cheok.org/stacktrace/server.php");
        mActivityRecognitionClient =
                new ActivityRecognitionClient(this, this, this);
        /*
         * Create the PendingIntent that Location Services uses
         * to send activity recognition updates back to this app.
         */
        Intent intent = new Intent(
                this, ActivityRecognitionService.class);
        /*
         * Return a PendingIntent that starts the IntentService.
         */
        mActivityRecognitionPendingIntent =
                PendingIntent.getService(this, 0, intent,
                PendingIntent.FLAG_UPDATE_CURRENT);
        mActivityRecognitionClient.connect();
		super.onCreate();
	}
	
	public void startUpdates() {
        // Check for Google Play services

        if (!servicesConnected()) {
            return;
        }
        // If a request is not already underway
        if (!mInProgress) {
            // Indicate that a request is in progress
            mInProgress = true;
            // Request a connection to Location Services
            mActivityRecognitionClient.connect();
        //
        } else {
            /*
             * A request is already underway. You can handle
             * this situation by disconnecting the client,
             * re-setting the flag, and then re-trying the
             * request.
             */
        }
	}
	
	/**
     * Map detected activity types to strings
     *@param activityType The detected activity type
     *@return A user-readable name for the type
     */
    private String getNameFromType(int activityType) {
        switch(activityType) {
            case DetectedActivity.IN_VEHICLE:
                return "in_vehicle";
            case DetectedActivity.ON_BICYCLE:
                return "on_bicycle";
            case DetectedActivity.ON_FOOT:
                return "on_foot";
            case DetectedActivity.STILL:
                return "still";
            case DetectedActivity.UNKNOWN:
                return "unknown";
            case DetectedActivity.TILTING:
                return "tilting";
        }
        return "unknown";
    }

	/* (non-Javadoc)
	 * @see android.app.IntentService#onStartCommand(android.content.Intent, int, int)
	 */
	@Override
	public int onStartCommand(Intent intent, int flags, int startId) {
		return super.onStartCommand(intent, flags, startId);
		//return START_STICKY;
	}

	/**
	 * @return the tempActivity
	 */
	public static SActivity getTempActivity() {
		return tempActivity;
	}
}
